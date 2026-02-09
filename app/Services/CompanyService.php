<?php

namespace App\Services;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\DTO\CompanyDTO;
use App\Exceptions\CompanyValidationException;
use App\Exceptions\CompanyNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service responsável pela lógica de negócio de empresas
 * 
 * Esta classe contém as regras de negócio e validações para operações
 * relacionadas às empresas.
 */
class CompanyService
{
    /**
     * Construtor do serviço
     * 
     * @param CompanyRepository $companyRepository Repository de empresas injetado via DI
     */
    public function __construct(
        private CompanyRepository $companyRepository
    ) {}

    /**
     * Lista todas as empresas com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de empresas
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->companyRepository->listar($perPage);
    }

    /**
     * Obtém uma empresa pelo ID
     * 
     * @param int $id ID da empresa
     * @return Company Empresa encontrada
     * @throws CompanyNotFoundException Se a empresa não for encontrada
     */
    public function obter(int $id): Company
    {
        $company = $this->companyRepository->obterPorId($id);
        
        if (!$company) {
            throw new CompanyNotFoundException("Company com ID {$id} não encontrado");
        }
        
        return $company;
    }

    /**
     * Cria uma nova empresa
     * 
     * @param CompanyDTO $dto Objeto com os dados da empresa
     * @return Company Empresa criada
     * @throws CompanyValidationException Se houver erro de validação
     */
    public function incluir(CompanyDTO $dto): Company
    {
        // Validações de negócio
        if ($this->companyRepository->existe('email', $dto->email)) {
            throw new CompanyValidationException('Email já está em uso por outra empresa');
        }

        if ($this->companyRepository->existe('cnpj', $dto->cnpj)) {
            throw new CompanyValidationException('CNPJ já está cadastrado');
        }

        if ($dto->max_users < 1) {
            throw new CompanyValidationException('A empresa deve permitir pelo menos 1 usuário');
        }

        $dados = $dto->toArray();
        
        return $this->companyRepository->incluir($dados);
    }

    /**
     * Atualiza uma empresa existente
     *
     * @param int $id ID da empresa a ser atualizada
     * @param CompanyDTO $dto Objeto com os novos dados
     * @return Company Empresa atualizada
     * @throws CompanyNotFoundException Se a empresa não for encontrada
     * @throws CompanyValidationException Se houver erro na atualização
     */
    public function atualizar(int $id, CompanyDTO $dto): Company
    {
        // Verifica se a empresa existe
        if (!$this->companyRepository->obterPorId($id)) {
            throw new CompanyNotFoundException("Empresa com ID {$id} não encontrada");
        }

        // Validações de negócio
        if ($dto->email && $this->companyRepository->existe('email', $dto->email, $id)) {
            throw new CompanyValidationException('Email já está em uso por outra empresa');
        }

        if ($dto->cnpj && $this->companyRepository->existe('cnpj', $dto->cnpj, $id)) {
            throw new CompanyValidationException('CNPJ já está cadastrado');
        }

        if (isset($dto->max_users) && $dto->max_users < 1) {
            throw new CompanyValidationException('A empresa deve permitir pelo menos 1 usuário');
        }

        $dados = array_filter($dto->toArray(), function ($value) {
            return $value !== null;
        });
        
        $companyAtualizado = $this->companyRepository->atualizar($id, $dados);
        
        if (!$companyAtualizado) {
            throw new CompanyValidationException("Erro ao atualizar empresa com ID {$id}");
        }
        
        return $companyAtualizado;
    }

    /**
     * Exclui uma empresa
     *
     * @param int $id ID da empresa
     * @return bool True se excluído com sucesso
     * @throws CompanyNotFoundException Se a empresa não for encontrada
     * @throws CompanyValidationException Se houver dependências
     */
    public function excluir(int $id): bool
    {
        // Verifica se a empresa existe
        $company = $this->companyRepository->obterPorId($id);
        if (!$company) {
            throw new CompanyNotFoundException("Empresa com ID {$id} não encontrada");
        }

        // Verifica se há usuários vinculados
        if ($company->users()->count() > 0) {
            throw new CompanyValidationException('Não é possível excluir empresa com usuários cadastrados');
        }

        return $this->companyRepository->excluir($id);
    }

    /**
     * Busca empresas por critério específico
     * 
     * @param string $campo Campo a ser pesquisado
     * @param mixed $valor Valor a ser buscado
     * @return \Illuminate\Database\Eloquent\Collection Coleção de empresas
     */
    public function buscarPor(string $campo, $valor)
    {
        return $this->companyRepository->buscarPor($campo, $valor);
    }

    /**
     * Verifica se uma empresa existe
     * 
     * @param string $campo Campo a ser verificado
     * @param mixed $valor Valor a ser buscado
     * @param int|null $excludeId ID a ser excluído da busca
     * @return bool True se existir
     */
    public function existe(string $campo, $valor, ?int $excludeId = null): bool
    {
        return $this->companyRepository->existe($campo, $valor, $excludeId);
    }

    // Adicione métodos específicos do seu domínio aqui
    // Exemplo:
    // public function ativar(int $id): Company
    // {
    //     $company = $this->obter($id);
    //     return $this->companyRepository->atualizar($id, ['status' => 'active']);
    // }
}