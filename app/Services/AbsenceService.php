<?php

namespace App\Services;

use App\Models\Absence;
use App\Repositories\AbsenceRepository;
use App\DTO\AbsenceDTO;
use App\Exceptions\AbsenceValidationException;
use App\Exceptions\AbsenceNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service responsável pela lógica de negócio de ausências
 * 
 * Esta classe contém as regras de negócio e validações para operações
 * relacionadas às ausências dos funcionários (faltas, atestados, férias).
 */
class AbsenceService
{
    /**
     * Construtor do serviço
     * 
     * @param AbsenceRepository $absenceRepository Repository de ausências injetado via DI
     */
    public function __construct(
        private AbsenceRepository $absenceRepository
    ) {}

    /**
     * Lista todas as ausências com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de ausências
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->absenceRepository->listar($perPage);
    }

    /**
     * Obtém uma ausência pelo ID
     * 
     * @param int $id ID da ausência
     * @return Absence Ausência encontrada
     * @throws AbsenceNotFoundException Se a ausência não for encontrada
     */
    public function obter(int $id): Absence
    {
        $absence = $this->absenceRepository->obterPorId($id);
        
        if (!$absence) {
            throw new AbsenceNotFoundException("Absence com ID {$id} não encontrado");
        }
        
        return $absence;
    }

    /**
     * Cria um novo registro de ausência
     * 
     * @param AbsenceDTO $dto Objeto com os dados da ausência
     * @return Absence Ausência criada
     */
    public function incluir(AbsenceDTO $dto): Absence
    {
        // Validações de negócio aqui
        // Exemplo:
        // if ($this->absenceRepository->existe('email', $dto->email)) {
        //     throw new \InvalidArgumentException('Email já existe');
        // }

        $dados = $dto->toArray();
        
        return $this->absenceRepository->incluir($dados);
    }

    /**
     * Atualiza uma ausência existente
     *
     * @param int $id ID da ausência a ser atualizada
     * @param AbsenceDTO $dto Objeto com os novos dados
     * @return Absence Ausência atualizada
     * @throws AbsenceNotFoundException Se a ausência não for encontrada
     * @throws AbsenceValidationException Se houver erro na atualização
     */
    public function atualizar(int $id, AbsenceDTO $dto): Absence
    {
        // Verifica se o Absence existe
        if (!$this->absenceRepository->obterPorId($id)) {
            throw new AbsenceNotFoundException("Absence com ID {$id} não encontrado");
        }        // Validações de negócio aqui
        // Exemplo:
        // if ($dto->email && $this->absenceRepository->existe('email', $dto->email, $id)) {
        //     throw new \InvalidArgumentException('Email já existe');
        // }

        $dados = array_filter($dto->toArray(), function ($value) {
            return $value !== null;
        });
        
        $absenceAtualizado = $this->absenceRepository->atualizar($id, $dados);
        
        if (!$absenceAtualizado) {
            throw new AbsenceValidationException("Erro ao atualizar Absence com ID {$id}");
        }
        
        return $absenceAtualizado;
    }

    /**
     * Exclui um Absence
     *
     * @param int $id
     * @return bool
     * @throws AbsenceNotFoundException
     */
    public function excluir(int $id): bool
    {
        // Verifica se o Absence existe
        if (!$this->absenceRepository->obterPorId($id)) {
            throw new AbsenceNotFoundException("Absence com ID {$id} não encontrado");
        }        // Validações de negócio para exclusão aqui
        // Exemplo:
        // if ($this->temDependencias($id)) {
        //     throw new \InvalidArgumentException('Não é possível excluir, há registros dependentes');
        // }

        return $this->absenceRepository->excluir($id);
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
        return $this->absenceRepository->buscarPor($campo, $valor);
    }

    /**
     * Verifica se um Absence existe
     * 
     * @param string $campo
     * @param mixed $valor
     * @param int|null $excludeId
     * @return bool
     */
    public function existe(string $campo, $valor, ?int $excludeId = null): bool
    {
        return $this->absenceRepository->existe($campo, $valor, $excludeId);
    }

    /**
     * Aprova uma ausência
     * 
     * @param int $id
     * @param int $approverId
     * @return Absence
     * @throws AbsenceNotFoundException
     */
    public function aprovar(int $id, int $approverId): Absence
    {
        $absence = $this->obter($id);
        
        if (!$absence->isPending()) {
            throw new AbsenceValidationException("Apenas ausências pendentes podem ser aprovadas");
        }
        
        $absence->approve($approverId);
        
        return $absence->fresh();
    }

    /**
     * Rejeita uma ausência
     * 
     * @param int $id
     * @param int $approverId
     * @return Absence
     * @throws AbsenceNotFoundException
     */
    public function rejeitar(int $id, int $approverId): Absence
    {
        $absence = $this->obter($id);
        
        if (!$absence->isPending()) {
            throw new AbsenceValidationException("Apenas ausências pendentes podem ser rejeitadas");
        }
        
        $absence->reject($approverId);
        
        return $absence->fresh();
    }

    /**
     * Lista ausências pendentes
     * 
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listarPendentes(int $perPage = 15): LengthAwarePaginator
    {
        return $this->absenceRepository->listarPendentes($perPage);
    }

    /**
     * Busca ausências de um usuário
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscarPorUsuario(int $userId)
    {
        return $this->absenceRepository->buscarPor('user_id', $userId);
    }
}