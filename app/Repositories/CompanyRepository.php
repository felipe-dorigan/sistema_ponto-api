<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository responsável pela persistência de dados de companys
 * 
 * Esta classe gerencia todas as operações de banco de dados relacionadas
 * aos companys.
 */
class CompanyRepository extends Repository
{
    /**
     * Construtor do repository
     * 
     * @param Company $company Model de company injetado via DI
     */
    public function __construct(Company $company)
    {
        parent::__construct($company);
    }

    /**
     * Lista companys com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de companys
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca um company pelo ID
     * 
     * @param int $id ID do company
     * @return Company|null Company encontrado ou null
     */
    public function obterPorId(int $id): ?Company
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo company
     * 
     * @param array $dados Dados do company
     * @return Company Company criado
     */
    public function incluir(array $dados): Company
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um company existente
     * 
     * @param int $id ID do company a ser atualizado
     * @param array $dados Dados para atualização
     * @return Company|null Company atualizado ou null se não encontrado
     */
    public function atualizar(int $id, array $dados): ?Company
    {
        $company = $this->obterPorId($id);
        
        if ($company) {
            $company->update($dados);
            return $company->fresh();
        }
        
        return null;
    }

    /**
     * Remove um Company
     * 
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool
    {
        $company = $this->obterPorId($id);
        
        if ($company) {
            return $company->delete();
        }
        
        return false;
    }

    /**
     * Busca Companys por critério específico
     * 
     * @param string $campo
     * @param mixed $valor
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscarPor(string $campo, $valor)
    {
        return $this->model->where($campo, $valor)->get();
    }

    /**
     * Verifica se existe um Company com determinado critério
     * 
     * @param string $campo
     * @param mixed $valor
     * @param int|null $excludeId
     * @return bool
     */
    public function existe(string $campo, $valor, ?int $excludeId = null): bool
    {
        $query = $this->model->where($campo, $valor);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}