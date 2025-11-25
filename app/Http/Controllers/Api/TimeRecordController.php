<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeRecordController extends Controller
{
    /**
     * Listar registros de ponto do usuário
     */
    public function index(Request $request)
    {
        $query = TimeRecord::where('user_id', $request->user()->id)
            ->orderBy('date', 'desc');

        // Filtrar por período se informado
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $records = $query->paginate(31); // 31 dias por página

        return response()->json($records);
    }

    /**
     * Registrar/atualizar ponto do dia
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i',
            'lunch_start' => 'nullable|date_format:H:i',
            'lunch_end' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $expectedMinutes = $user->daily_work_hours * 60;

        $record = TimeRecord::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => $request->date,
            ],
            [
                'entry_time' => $request->entry_time,
                'exit_time' => $request->exit_time,
                'lunch_start' => $request->lunch_start,
                'lunch_end' => $request->lunch_end,
                'expected_minutes' => $expectedMinutes,
                'notes' => $request->notes,
            ]
        );

        // Calcular minutos trabalhados
        $workedMinutes = $this->calculateWorkedMinutes(
            $request->entry_time,
            $request->exit_time,
            $request->lunch_start,
            $request->lunch_end
        );

        $record->update(['worked_minutes' => $workedMinutes]);

        return response()->json([
            'message' => 'Ponto registrado com sucesso',
            'record' => $record->fresh(),
            'balance_minutes' => $record->getBalanceMinutes(),
        ], 201);
    }

    /**
     * Obter registro específico
     */
    public function show($id, Request $request)
    {
        $record = TimeRecord::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($record);
    }

    /**
     * Calcular banco de horas
     */
    public function hourBank(Request $request)
    {
        $userId = $request->user()->id;

        $records = TimeRecord::where('user_id', $userId);

        // Filtrar por período se informado
        if ($request->has('start_date')) {
            $records->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $records->where('date', '<=', $request->end_date);
        }

        $totalWorkedMinutes = $records->sum('worked_minutes');
        $totalExpectedMinutes = $records->sum('expected_minutes');
        $balanceMinutes = $totalWorkedMinutes - $totalExpectedMinutes;

        $hours = floor(abs($balanceMinutes) / 60);
        $minutes = abs($balanceMinutes) % 60;

        return response()->json([
            'total_worked_minutes' => $totalWorkedMinutes,
            'total_expected_minutes' => $totalExpectedMinutes,
            'balance_minutes' => $balanceMinutes,
            'balance_formatted' => ($balanceMinutes >= 0 ? '+' : '-') . sprintf('%02d:%02d', $hours, $minutes),
            'total_days' => $records->count(),
        ]);
    }

    /**
     * Marcar entrada rápida (registra horário atual)
     */
    public function quickEntry(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i');

        $record = TimeRecord::firstOrCreate(
            [
                'user_id' => $user->id,
                'date' => $date,
            ],
            [
                'expected_minutes' => $user->daily_work_hours * 60,
            ]
        );

        // Determinar qual campo atualizar
        if (!$record->entry_time) {
            $record->entry_time = $time;
            $message = 'Entrada registrada';
        } elseif (!$record->lunch_start) {
            $record->lunch_start = $time;
            $message = 'Início do almoço registrado';
        } elseif (!$record->lunch_end) {
            $record->lunch_end = $time;
            $message = 'Fim do almoço registrado';
        } elseif (!$record->exit_time) {
            $record->exit_time = $time;
            $message = 'Saída registrada';
        } else {
            return response()->json([
                'message' => 'Todos os horários já foram registrados hoje',
                'record' => $record,
            ], 400);
        }

        $record->save();

        // Recalcular minutos trabalhados
        $workedMinutes = $this->calculateWorkedMinutes(
            $record->entry_time,
            $record->exit_time,
            $record->lunch_start,
            $record->lunch_end
        );
        $record->update(['worked_minutes' => $workedMinutes]);

        return response()->json([
            'message' => $message,
            'record' => $record->fresh(),
        ]);
    }

    /**
     * Método auxiliar para calcular minutos trabalhados
     */
    private function calculateWorkedMinutes($entryTime, $exitTime, $lunchStart, $lunchEnd)
    {
        if (!$entryTime || !$exitTime) {
            return 0;
        }

        $entry = strtotime($entryTime);
        $exit = strtotime($exitTime);
        $totalMinutes = ($exit - $entry) / 60;

        // Subtrai o tempo de almoço se existir
        if ($lunchStart && $lunchEnd) {
            $lunchStartTime = strtotime($lunchStart);
            $lunchEndTime = strtotime($lunchEnd);
            $lunchMinutes = ($lunchEndTime - $lunchStartTime) / 60;
            $totalMinutes -= $lunchMinutes;
        }

        return max(0, $totalMinutes);
    }
}
