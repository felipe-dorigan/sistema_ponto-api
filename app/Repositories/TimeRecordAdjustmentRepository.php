<?php

namespace App\Repositories;

use App\Models\TimeRecordAdjustment;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository responsável pela persistência de dados de time-record-adjustments
 * 
 * Esta classe gerencia todas as operações de banco de dados relacionadas
 * aos time-record-adjustments.
 */
class TimeRecordAdjustmentRepository extends Repository
{
    /**
     * Construtor do repository
     * 
     * @param TimeRecordAdjustment $time_record_adjustment Model de time-record-adjustment injetado via DI
     */
    public function __construct(TimeRecordAdjustment $time_record_adjustment)
    {
        parent::__construct($time_record_adjustment);
    }

    /**
     * Lista time-record-adjustments com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de time-record-adjustments
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca um time-record-adjustment pelo ID
     * 
     * @param int $id ID do time-record-adjustment
     * @return TimeRecordAdjustment|null TimeRecordAdjustment encontrado ou null
     */
    public function obterPorId(int $id): ?TimeRecordAdjustment
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo time-record-adjustment
     * 
     * @param array $dados Dados do time-record-adjustment
     * @return TimeRecordAdjustment TimeRecordAdjustment criado
     */
    public function incluir(array $dados): TimeRecordAdjustment
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um time-record-adjustment existente
     * 
     * @param int $id ID do time-record-adjustment a ser atualizado
     * @param array $dados Dados para atualização
     * @return TimeRecordAdjustment|null TimeRecordAdjustment atualizado ou null se não encontrado
     */
    public function atualizar(int $id, array $dados): ?TimeRecordAdjustment
    {
        $time_record_adjustment = $this->obterPorId($id);
        
        if ($time_record_adjustment) {
            $time_record_adjustment->update($dados);
            return $time_record_adjustment->fresh();
        }
        
        return null;
    }

    /**
     * Remove um TimeRecordAdjustment
     * 
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool
    {
        $time_record_adjustment = $this->obterPorId($id);
        
        if ($time_record_adjustment) {
            return $time_record_adjustment->delete();
        }
        
        return false;
    }

    /**
     * Busca TimeRecordAdjustments por critério específico
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
     * Verifica se existe um TimeRecordAdjustment com determinado critério
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