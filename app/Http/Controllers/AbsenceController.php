<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Services\AbsenceService;
use App\DTO\AbsenceDTO;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AbsenceController extends Controller
{
    public function __construct(
        private AbsenceService $absenceService
    ) {}

    /**
     * Lista todos os absences paginados
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function listar(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $absence = $this->absenceService->listar($perPage);
            
            return ApiResponse::success(
                $absence,
                'Absences listados com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao listar absences: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Cria um novo absence
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function incluir(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'required|string|max:255',
            'description' => 'required|string',
            'approved_at' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = AbsenceDTO::fromRequest($request->all());
            $absence = $this->absenceService->incluir($dto);
            
            return ApiResponse::success(
                $absence,
                'Absence criado com sucesso',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao criar absence: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Busca um absence específico
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function obterPorCodigo(int $id): JsonResponse
    {
        try {
            $absence = $this->absenceService->obter($id);
            
            return ApiResponse::success(
                $absence,
                'Absence encontrado'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao buscar absence: ' . $e->getMessage(),
                404
            );
        }
    }

    /**
     * Atualiza um absence existente
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function atualizar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'sometimes',
            'start_time' => 'sometimes',
            'end_time' => 'sometimes',
            'reason' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'approved_at' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = AbsenceDTO::fromRequest($request->all());
            $absence = $this->absenceService->atualizar($id, $dto);
            
            return ApiResponse::success(
                $absence,
                'Absence atualizado com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao atualizar absence: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove um absence
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function excluir(int $id): JsonResponse
    {
        try {
            $this->absenceService->excluir($id);
            
            return ApiResponse::success(
                null,
                'Absence removido com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao remover absence: ' . $e->getMessage(),
                500
            );
        }
    }
}