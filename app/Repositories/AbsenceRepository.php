<?php

namespace App\Repositories;

use App\Models\Absence;
use Illuminate\Pagination\LengthAwarePaginator;

class AbsenceRepository extends Repository
{
    /**
     * AbsenceRepository constructor.
     * 
     * @param Absence $absence
     */
    public function __construct(Absence $absence)
    {
        parent::__construct($absence);
    }

    /**
     * Lista Absences paginados
     * 
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca Absence por ID
     * 
     * @param int $id
     * @return Absence|null
     */
    public function obterPorId(int $id): ?Absence
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo Absence
     * 
     * @param array $dados
     * @return Absence
     */
    public function incluir(array $dados): Absence
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um Absence
     * 
     * @param int $id
     * @param array $dados
     * @return Absence|null
     */
    public function atualizar(int $id, array $dados): ?Absence
    {
        $absence = $this->obterPorId($id);
        
        if ($absence) {
            $absence->update($dados);
            return $absence->fresh();
        }
        
        return null;
    }

    /**
     * Remove um Absence
     * 
     * @param int $id
     * @return bool
     */
    public function excluir(int $id): bool
    {
        $absence = $this->obterPorId($id);
        
        if ($absence) {
            return $absence->delete();
        }
        
        return false;
    }

    /**
     * Busca Absences por critério específico
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
     * Verifica se existe um Absence com determinado critério
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
     * Lista ausências pendentes paginadas
     * 
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listarPendentes(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}