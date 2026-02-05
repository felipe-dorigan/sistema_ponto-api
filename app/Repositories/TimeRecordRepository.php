<?php

namespace App\Repositories;

use App\Models\TimeRecord;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository responsável pela persistência de dados de registros de ponto
 * 
 * Esta classe gerencia todas as operações de banco de dados relacionadas
 * aos registros de ponto dos funcionários.
 */
class TimeRecordRepository extends Repository
{
    /**
     * Construtor do repository
     * 
     * @param TimeRecord $time_record Model de registro de ponto injetado via DI
     */
    public function __construct(TimeRecord $time_record)
    {
        parent::__construct($time_record);
    }

    /**
     * Lista registros de ponto com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de registros
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca um registro de ponto pelo ID
     * 
     * @param int $id ID do registro de ponto
     * @return TimeRecord|null Registro encontrado ou null
     */
    public function obterPorId(int $id): ?TimeRecord
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo registro de ponto
     * 
     * @param array $dados Dados do registro de ponto
     * @return TimeRecord Registro criado
     */
    public function incluir(array $dados): TimeRecord
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um TimeRecord
     * 
     * @param int $id
     * @param array $dados
     * @return TimeRecord|null
     */
    public function atualizar(int $id, array $dados): ?TimeRecord
    {
        $time_record = $this->obterPorId($id);
        
        if ($time_record) {
            $time_record->update($dados);
            return $time_record->fresh();
        }
        
        return null;
    }

    /**
     * Remove um TimeRecord
     * 
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool
    {
        $time_record = $this->obterPorId($id);
        
        if ($time_record) {
            return $time_record->delete();
        }
        
        return false;
    }

    /**
     * Busca TimeRecords por critério específico
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
     * Verifica se existe um TimeRecord com determinado critério
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

    /**
     * Busca registros de ponto de um usuário em um período
     * 
     * @param int $userId
     * @param string $dataInicio
     * @param string $dataFim
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscarPorUsuarioEPeriodo(int $userId, string $dataInicio, string $dataFim)
    {
        return $this->model
            ->where('user_id', $userId)
            ->whereBetween('date', [$dataInicio, $dataFim])
            ->orderBy('date', 'desc')
            ->get();
    }
}