<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository
{
    /**
     * O modelo Eloquent associado ao repositório.
     */
    protected Model $model;

    /**
     * Construtor que define o modelo a ser usado.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Cria um novo registro.
     * Este método é genérico e espera um array de dados.
     */
    public function incluir(array $dados): Model
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um registro existente.
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
     * Encontra um registro pelo seu ID.
     */
    public function obterPorCodigo(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Lista os registros com paginação.
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Exclui um registro pelo seu ID.
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