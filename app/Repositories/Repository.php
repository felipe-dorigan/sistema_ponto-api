<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository base abstrato
 * 
 * Fornece métodos genéricos de persistência de dados que podem
 * ser herdados por repositories específicos.
 */
abstract class Repository
{
    /**
     * O modelo Eloquent associado ao repository
     * 
     * @var Model
     */
    protected Model $model;

    /**
     * Construtor que define o modelo a ser usado
     * 
     * @param Model $model Instância do modelo Eloquent
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Cria um novo registro no banco de dados
     * 
     * @param array $dados Dados do registro a ser criado
     * @return Model Registro criado
     */
    public function incluir(array $dados): Model
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um registro existente
     * 
     * @param int $id ID do registro a ser atualizado
     * @param array $dados Dados para atualização
     * @return Model|null Registro atualizado ou null se não encontrado
     */
    public function atualizar(int $id, array $dados): ?Model
    {
        $registro = $this->obterPorCodigo($id);
        if ($registro) {
            $registro->update($dados);
            return $registro;
        }
        return null;
    }

    /**
     * Encontra um registro pelo seu ID
     * 
     * @param int $id ID do registro
     * @return Model|null Registro encontrado ou null
     */
    public function obterPorCodigo(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Lista os registros com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de registros
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Exclui um registro pelo seu ID
     * 
     * @param int $id ID do registro a ser excluído
     * @return bool True se excluído com sucesso, false se não encontrado
     */
    public function excluir(int $id): bool
    {
        $registro = $this->obterPorCodigo($id);
        if ($registro) {
            return $registro->delete();
        }
        return false;
    }
}