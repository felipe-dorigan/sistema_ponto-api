<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyService;
use App\DTO\CompanyDTO;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Controller responsável pelo gerenciamento de empresas
 * 
 * Este controller gerencia todas as operações CRUD relacionadas às empresas.
 */
class CompanyController extends Controller
{
    /**
     * Construtor do controller
     * 
     * @param CompanyService $companyService Serviço de empresas injetado via DI
     */
    public function __construct(
        private CompanyService $companyService
    ) {}

    /**
     * Lista todas as empresas com paginação
     * 
     * @param Request $request Requisição contendo parâmetros de paginação
     * @return JsonResponse Lista paginada de empresas
     */
    public function listar(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $company = $this->companyService->listar($perPage);
            
            return ApiResponse::success(
                $company,
                'Empresas listadas com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao listar empresas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Cria uma nova empresa
     * 
     * @param Request $request Requisição contendo os dados da empresa
     * @return JsonResponse Empresa criada ou erro de validação
     */
    public function incluir(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/|unique:companies,cnpj',
            'email' => 'required|email|max:255|unique:companies,email',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'zip_code' => 'required|string|regex:/^\d{5}-\d{3}$/',
            'max_users' => 'required|integer|min:1',
            'active' => 'required|boolean'
        ], [
            'cnpj.regex' => 'CNPJ deve estar no formato 00.000.000/0000-00',
            'cnpj.unique' => 'CNPJ já cadastrado',
            'email.unique' => 'Email já cadastrado',
            'email.email' => 'Email inválido',
            'state.size' => 'Estado deve ter 2 caracteres (UF)',
            'zip_code.regex' => 'CEP deve estar no formato 00000-000',
            'max_users.min' => 'A empresa deve permitir pelo menos 1 usuário'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = CompanyDTO::fromRequest($request->all());
            $company = $this->companyService->incluir($dto);
            
            return ApiResponse::success(
                $company,
                'Empresa criada com sucesso',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao criar empresa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Obtém uma empresa específica pelo ID
     * 
     * @param int $id ID da empresa
     * @return JsonResponse Empresa encontrada ou erro
     */
    public function obterPorCodigo(int $id): JsonResponse
    {
        try {
            $company = $this->companyService->obter($id);
            
            return ApiResponse::success(
                $company,
                'Empresa encontrada'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao buscar empresa: ' . $e->getMessage(),
                404
            );
        }
    }

    /**
     * Atualiza uma empresa existente
     * 
     * @param Request $request Requisição contendo os dados a serem atualizados
     * @param int $id ID da empresa
     * @return JsonResponse Empresa atualizada ou erro
     */
    public function atualizar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'cnpj' => 'sometimes|string|regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/|unique:companies,cnpj,' . $id,
            'email' => 'sometimes|email|max:255|unique:companies,email,' . $id,
            'phone' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|size:2',
            'zip_code' => 'sometimes|string|regex:/^\d{5}-\d{3}$/',
            'max_users' => 'sometimes|integer|min:1',
            'active' => 'sometimes|boolean'
        ], [
            'cnpj.regex' => 'CNPJ deve estar no formato 00.000.000/0000-00',
            'cnpj.unique' => 'CNPJ já cadastrado',
            'email.unique' => 'Email já cadastrado',
            'email.email' => 'Email inválido',
            'state.size' => 'Estado deve ter 2 caracteres (UF)',
            'zip_code.regex' => 'CEP deve estar no formato 00000-000',
            'max_users.min' => 'A empresa deve permitir pelo menos 1 usuário'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $dto = CompanyDTO::fromRequest($request->all());
            $company = $this->companyService->atualizar($id, $dto);
            
            return ApiResponse::success(
                $company,
                'Empresa atualizada com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao atualizar empresa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove uma empresa
     * 
     * @param int $id ID da empresa
     * @return JsonResponse Resultado da exclusão
     */
    public function excluir(int $id): JsonResponse
    {
        try {
            $this->companyService->excluir($id);
            
            return ApiResponse::success(
                null,
                'Empresa removida com sucesso'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Erro ao remover empresa: ' . $e->getMessage(),
                500
            );
        }
    }
}