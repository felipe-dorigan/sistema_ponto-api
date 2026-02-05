<?php

namespace App\Repositories;

use App\Models\Absence;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository responsável pela persistência de dados de ausências
 * 
 * Esta classe gerencia todas as operações de banco de dados relacionadas
 * às ausências dos funcionários (faltas, atestados, férias).
 */
class AbsenceRepository extends Repository
{
    /**
     * Construtor do repository
     * 
     * @param Absence $absence Model de ausência injetado via DI
     */
    public function __construct(Absence $absence)
    {
        parent::__construct($absence);
    }

    /**
     * Lista ausências com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de ausências
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Busca uma ausência pelo ID
     * 
     * @param int $id ID da ausência
     * @return Absence|null Ausência encontrada ou null
     */
    public function obterPorId(int $id): ?Absence
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo registro de ausência
     * 
     * @param array $dados Dados da ausência
     * @return Absence Ausência criada
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