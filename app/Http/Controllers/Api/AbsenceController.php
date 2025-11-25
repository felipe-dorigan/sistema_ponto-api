<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * Listar ausências do usuário
     */
    public function index(Request $request)
    {
        $query = Absence::where('user_id', $request->user()->id)
            ->with('approver')
            ->orderBy('date', 'desc');

        // Filtrar por status se informado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $absences = $query->paginate(20);

        return response()->json($absences);
    }

    /**
     * Registrar nova ausência
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $absence = Absence::create([
            'user_id' => $request->user()->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Ausência registrada com sucesso',
            'absence' => $absence,
        ], 201);
    }

    /**
     * Obter ausência específica
     */
    public function show($id, Request $request)
    {
        $absence = Absence::where('user_id', $request->user()->id)
            ->with('approver')
            ->findOrFail($id);

        return response()->json($absence);
    }

    /**
     * Aprovar/rejeitar ausência (apenas admin)
     */
    public function updateStatus($id, Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Acesso negado. Apenas administradores podem aprovar ausências.',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $absence = Absence::findOrFail($id);

        $absence->update([
            'status' => $request->status,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Status da ausência atualizado com sucesso',
            'absence' => $absence->fresh()->load('approver'),
        ]);
    }

    /**
     * Listar todas as ausências (apenas admin)
     */
    public function indexAll(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Acesso negado.',
            ], 403);
        }

        $query = Absence::with(['user', 'approver'])
            ->orderBy('date', 'desc');

        // Filtrar por status se informado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $absences = $query->paginate(20);

        return response()->json($absences);
    }
}
