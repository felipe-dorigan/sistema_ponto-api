<?php

namespace App\Http\Controllers;

use App\Models\TimeRecordAdjustment;
use App\Services\TimeRecordAdjustmentService;
use App\DTO\TimeRecordAdjustmentDTO;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Controller responsável pelo gerenciamento de TimeRecordAdjustment
 * 
 * Este controller gerencia todas as operações CRUD relacionadas ao modelo TimeRecordAdjustment.
 */
class TimeRecordAdjustmentController extends Controller
{
    /**
     * Construtor do controller
     * 
     * @param TimeRecordAdjustmentService $time_record_adjustmentService Serviço injetado via DI
     */
    public function __construct(
        private TimeRecordAdjustmentService $time_record_adjustmentService
    ) {}

    /**
     * Lista todos os registros com paginação
     * 
     * @param Request $request Requisição contendo parâmetros de paginação
     * @return JsonResponse Lista paginada de registros
     */
    public function listar(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $time_record_adjustment = $this->time_record_adjustmentService->listar($perPage);
            
            return ApiResponse::success(
                $time_record_adjustment,
                'Registros listados com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao listar registros: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Cria um novo registro
     * 
     * @param Request $request Requisição contendo os dados do registro
     * @return JsonResponse Registro criado ou erro de validação
     */
    public function incluir(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'time_record_id' => 'required|integer|exists:time_records,id',
            'user_id' => 'required|integer|exists:users,id',
            'field_to_change' => 'required|in:entry_time,exit_time,lunch_start,lunch_end,date,notes',
            'current_value' => 'nullable|string',
            'requested_value' => 'required|string',
            'reason' => 'required|string|min:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = TimeRecordAdjustmentDTO::fromRequest($request->all());
            $time_record_adjustment = $this->time_record_adjustmentService->incluir($dto);
            
            return ApiResponse::success(
                $time_record_adjustment,
                'Registro criado com sucesso',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao criar registro: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Obtém um registro específico pelo ID
     * 
     * @param int $id ID do registro
     * @return JsonResponse Registro encontrado ou erro
     */
    public function obterPorCodigo(int $id): JsonResponse
    {
        try {
            $time_record_adjustment = $this->time_record_adjustmentService->obter($id);
            
            return ApiResponse::success(
                $time_record_adjustment,
                'Registro encontrado'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao buscar registro: ' . $e->getMessage(),
                404
            );
        }
    }

    /**
     * Atualiza um registro existente
     * 
     * @param Request $request Requisição contendo os dados a serem atualizados
     * @param int $id ID do registro
     * @return JsonResponse Registro atualizado ou erro
     */
    public function atualizar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'time_record_id' => 'sometimes|integer|min:0',
            'user_id' => 'sometimes|integer|min:0',
            'current_value' => 'sometimes|string',
            'requested_value' => 'sometimes|string',
            'reason' => 'sometimes|string',
            'reviewed_by' => 'sometimes|integer|min:0',
            'reviewed_at' => 'sometimes',
            'admin_notes' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = TimeRecordAdjustmentDTO::fromRequest($request->all());
            $time_record_adjustment = $this->time_record_adjustmentService->atualizar($id, $dto);
            
            return ApiResponse::success(
                $time_record_adjustment,
                'Registro atualizado com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao atualizar registro: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove um registro
     * 
     * @param int $id ID do registro
     * @return JsonResponse Resultado da exclusão
     */
    public function excluir(int $id): JsonResponse
    {
        try {
            $this->time_record_adjustmentService->excluir($id);
            
            return ApiResponse::success(
                null,
                'Registro removido com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao remover registro: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Aprova uma solicitação de ajuste
     * 
     * @param int $id ID da solicitação
     * @param Request $request Requisição contendo admin_notes (opcional)
     * @return JsonResponse Solicitação aprovada ou erro
     */
    public function aprovar(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // TODO: Pegar o ID do usuário autenticado do token JWT
            $reviewerId = auth()->id();
            $adminNotes = $request->input('admin_notes');

            $adjustment = $this->time_record_adjustmentService->aprovar($id, $reviewerId, $adminNotes);
            
            return ApiResponse::success(
                $adjustment,
                'Solicitação aprovada com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao aprovar solicitação: ' . $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    /**
     * Rejeita uma solicitação de ajuste
     * 
     * @param int $id ID da solicitação
     * @param Request $request Requisição contendo admin_notes (opcional)
     * @return JsonResponse Solicitação rejeitada ou erro
     */
    public function rejeitar(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // TODO: Pegar o ID do usuário autenticado do token JWT
            $reviewerId = auth()->id();
            $adminNotes = $request->input('admin_notes');

            $adjustment = $this->time_record_adjustmentService->rejeitar($id, $reviewerId, $adminNotes);
            
            return ApiResponse::success(
                $adjustment,
                'Solicitação rejeitada com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao rejeitar solicitação: ' . $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}