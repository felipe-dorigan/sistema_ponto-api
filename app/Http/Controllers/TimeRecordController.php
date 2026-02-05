<?php

namespace App\Http\Controllers;

use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use App\DTO\TimeRecordDTO;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Controller responsável pelo gerenciamento de registros de ponto
 * 
 * Este controller gerencia todas as operações CRUD relacionadas aos
 * registros de ponto dos funcionários.
 */
class TimeRecordController extends Controller
{
    /**
     * Construtor do controller
     * 
     * @param TimeRecordService $time_recordService Serviço de registros de ponto injetado via DI
     */
    public function __construct(
        private TimeRecordService $time_recordService
    ) {}

    /**
     * Lista todos os registros de ponto com paginação
     * 
     * @param Request $request Requisição contendo parâmetros de paginação
     * @return JsonResponse Lista paginada de registros de ponto
     */
    public function listar(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $time_record = $this->time_recordService->listar($perPage);
            
            return ApiResponse::success(
                $time_record,
                'TimeRecords listados com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao listar time-records: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Cria um novo registro de ponto
     * 
     * @param Request $request Requisição contendo os dados do registro de ponto
     * @return JsonResponse Registro de ponto criado ou erro de validação
     */
    public function incluir(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'entry_time' => 'required',
            'exit_time' => 'required',
            'lunch_start' => 'required',
            'lunch_end' => 'required',
            'worked_minutes' => 'required|integer|min:0',
            'expected_minutes' => 'required|integer|min:0',
            'notes' => 'required|string',
            'entry_time_recorded_at' => 'required',
            'exit_time_recorded_at' => 'required',
            'lunch_start_recorded_at' => 'required',
            'lunch_end_recorded_at' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = TimeRecordDTO::fromRequest($request->all());
            $time_record = $this->time_recordService->incluir($dto);
            
            return ApiResponse::success(
                $time_record,
                'TimeRecord criado com sucesso',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao criar time-record: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Obtém um registro de ponto específico pelo ID
     * 
     * @param int $id ID do registro de ponto
     * @return JsonResponse Registro de ponto encontrado ou erro
     */
    public function obterPorCodigo(int $id): JsonResponse
    {
        try {
            $time_record = $this->time_recordService->obter($id);
            
            return ApiResponse::success(
                $time_record,
                'TimeRecord encontrado'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao buscar time-record: ' . $e->getMessage(),
                404
            );
        }
    }

    /**
     * Atualiza um time-record existente
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function atualizar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'sometimes',
            'entry_time' => 'sometimes',
            'exit_time' => 'sometimes',
            'lunch_start' => 'sometimes',
            'lunch_end' => 'sometimes',
            'worked_minutes' => 'sometimes|integer|min:0',
            'expected_minutes' => 'sometimes|integer|min:0',
            'notes' => 'sometimes|string',
            'entry_time_recorded_at' => 'sometimes',
            'exit_time_recorded_at' => 'sometimes',
            'lunch_start_recorded_at' => 'sometimes',
            'lunch_end_recorded_at' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = TimeRecordDTO::fromRequest($request->all());
            $time_record = $this->time_recordService->atualizar($id, $dto);
            
            return ApiResponse::success(
                $time_record,
                'TimeRecord atualizado com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao atualizar time-record: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove um time-record
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function excluir(int $id): JsonResponse
    {
        try {
            $this->time_recordService->excluir($id);
            
            return ApiResponse::success(
                null,
                'TimeRecord removido com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao remover time-record: ' . $e->getMessage(),
                500
            );
        }
    }
}