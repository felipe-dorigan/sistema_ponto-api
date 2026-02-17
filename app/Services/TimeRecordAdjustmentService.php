<?php

namespace App\Services;

use App\Models\TimeRecordAdjustment;
use App\Repositories\TimeRecordAdjustmentRepository;
use App\DTO\TimeRecordAdjustmentDTO;
use App\Exceptions\TimeRecordAdjustmentValidationException;
use App\Exceptions\TimeRecordAdjustmentNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service responsável pela lógica de negócio de TimeRecordAdjustment
 * 
 * Esta classe contém as regras de negócio e validações para operações
 * relacionadas ao modelo TimeRecordAdjustment.
 */
class TimeRecordAdjustmentService
{
    /**
     * Construtor do serviço
     * 
     * @param TimeRecordAdjustmentRepository $time_record_adjustmentRepository Repository injetado via DI
     */
    public function __construct(
        private TimeRecordAdjustmentRepository $time_record_adjustmentRepository
    ) {}

    /**
     * Lista todos os registros com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de registros
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->time_record_adjustmentRepository->listar($perPage);
    }

    /**
     * Obtém um registro pelo ID
     * 
     * @param int $id ID do registro
     * @return TimeRecordAdjustment Registro encontrado
     * @throws TimeRecordAdjustmentNotFoundException Se o registro não for encontrado
     */
    public function obter(int $id): TimeRecordAdjustment
    {
        $time_record_adjustment = $this->time_record_adjustmentRepository->obterPorId($id);
        
        if (!$time_record_adjustment) {
            throw new TimeRecordAdjustmentNotFoundException("TimeRecordAdjustment com ID {$id} não encontrado");
        }
        
        return $time_record_adjustment;
    }

    /**
     * Cria um novo registro
     * 
     * @param TimeRecordAdjustmentDTO $dto Objeto com os dados do registro
     * @return TimeRecordAdjustment Registro criado
     * @throws TimeRecordAdjustmentValidationException Se houver erro de validação
     */
    public function incluir(TimeRecordAdjustmentDTO $dto): TimeRecordAdjustment
    {
        // Validações de negócio aqui
        // Exemplo:
        // if ($this->time_record_adjustmentRepository->existe('email', $dto->email)) {
        //     throw new \InvalidArgumentException('Email já existe');
        // }

        $dados = $dto->toArray();
        
        return $this->time_record_adjustmentRepository->incluir($dados);
    }

    /**
     * Atualiza um registro existente
     *
     * @param int $id ID do registro a ser atualizado
     * @param TimeRecordAdjustmentDTO $dto Objeto com os novos dados
     * @return TimeRecordAdjustment Registro atualizado
     * @throws TimeRecordAdjustmentNotFoundException Se o registro não for encontrado
     * @throws TimeRecordAdjustmentValidationException Se houver erro na atualização
     */
    public function atualizar(int $id, TimeRecordAdjustmentDTO $dto): TimeRecordAdjustment
    {
        // Verifica se o registro existe
        if (!$this->time_record_adjustmentRepository->obterPorId($id)) {
            throw new TimeRecordAdjustmentNotFoundException("TimeRecordAdjustment com ID {$id} não encontrado");
        }

        // Validações de negócio aqui
        // Exemplo:
        // if ($dto->email && $this->time_record_adjustmentRepository->existe('email', $dto->email, $id)) {
        //     throw new TimeRecordAdjustmentValidationException('Email já existe');
        // }

        $dados = array_filter($dto->toArray(), function ($value) {
            return $value !== null;
        });
        
        $time_record_adjustmentAtualizado = $this->time_record_adjustmentRepository->atualizar($id, $dados);
        
        if (!$time_record_adjustmentAtualizado) {
            throw new TimeRecordAdjustmentValidationException("Erro ao atualizar TimeRecordAdjustment com ID {$id}");
        }
        
        return $time_record_adjustmentAtualizado;
    }

    /**
     * Exclui um registro
     *
     * @param int $id ID do registro
     * @return bool True se excluído com sucesso
     * @throws TimeRecordAdjustmentNotFoundException Se o registro não for encontrado
     * @throws TimeRecordAdjustmentValidationException Se houver dependências
     */
    public function excluir(int $id): bool
    {
        // Verifica se o registro existe
        if (!$this->time_record_adjustmentRepository->obterPorId($id)) {
            throw new TimeRecordAdjustmentNotFoundException("TimeRecordAdjustment com ID {$id} não encontrado");
        }

        // Validações de negócio para exclusão aqui
        // Exemplo:
        // if ($this->temDependencias($id)) {
        //     throw new TimeRecordAdjustmentValidationException('Não é possível excluir, há registros dependentes');
        // }

        return $this->time_record_adjustmentRepository->excluir($id);
    }

    /**
     * Busca registros por critério específico
     * 
     * @param string $campo Campo a ser pesquisado
     * @param mixed $valor Valor a ser buscado
     * @return \Illuminate\Database\Eloquent\Collection Coleção de registros
     */
    public function buscarPor(string $campo, $valor)
    {
        return $this->time_record_adjustmentRepository->buscarPor($campo, $valor);
    }

    /**
     * Verifica se um registro existe
     * 
     * @param string $campo Campo a ser verificado
     * @param mixed $valor Valor a ser buscado
     * @param int|null $excludeId ID a ser excluído da busca
     * @return bool True se existir
     */
    public function existe(string $campo, $valor, ?int $excludeId = null): bool
    {
        return $this->time_record_adjustmentRepository->existe($campo, $valor, $excludeId);
    }

    /**
     * Aprova uma solicitação de ajuste e aplica a mudança no registro de ponto
     * 
     * @param int $id ID da solicitação
     * @param int $reviewerId ID do administrador que está aprovando
     * @param string|null $adminNotes Observações do administrador
     * @return TimeRecordAdjustment Solicitação aprovada
     * @throws TimeRecordAdjustmentNotFoundException Se a solicitação não for encontrada
     * @throws TimeRecordAdjustmentValidationException Se houver erro na aprovação
     */
    public function aprovar(int $id, int $reviewerId, ?string $adminNotes = null): TimeRecordAdjustment
    {
        $adjustment = $this->obter($id);

        // Verifica se já foi revisado
        if ($adjustment->status !== 'pending') {
            throw new TimeRecordAdjustmentValidationException(
                "Esta solicitação já foi revisada com status: {$adjustment->status}"
            );
        }

        // Atualiza o status da solicitação
        $adjustmentAtualizado = $this->time_record_adjustmentRepository->atualizar($id, [
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $adminNotes
        ]);

        // Aplica o ajuste no registro de ponto original
        $timeRecord = $adjustment->timeRecord;
        if ($timeRecord) {
            $campo = $adjustment->field_to_change;
            $timeRecord->update([
                $campo => $adjustment->requested_value
            ]);

            // Recalcula worked_minutes se for alteração de horário
            if (in_array($campo, ['entry_time', 'exit_time', 'lunch_start', 'lunch_end'])) {
                $timeRecord->refresh();
                $workedMinutes = $this->calcularMinutosTrabalhados($timeRecord);
                $timeRecord->update(['worked_minutes' => $workedMinutes]);
            }
        }

        return $adjustmentAtualizado;
    }

    /**
     * Rejeita uma solicitação de ajuste
     * 
     * @param int $id ID da solicitação
     * @param int $reviewerId ID do administrador que está rejeitando
     * @param string|null $adminNotes Motivo da rejeição
     * @return TimeRecordAdjustment Solicitação rejeitada
     * @throws TimeRecordAdjustmentNotFoundException Se a solicitação não for encontrada
     * @throws TimeRecordAdjustmentValidationException Se houver erro na rejeição
     */
    public function rejeitar(int $id, int $reviewerId, ?string $adminNotes = null): TimeRecordAdjustment
    {
        $adjustment = $this->obter($id);

        // Verifica se já foi revisado
        if ($adjustment->status !== 'pending') {
            throw new TimeRecordAdjustmentValidationException(
                "Esta solicitação já foi revisada com status: {$adjustment->status}"
            );
        }

        // Atualiza o status da solicitação
        return $this->time_record_adjustmentRepository->atualizar($id, [
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $adminNotes
        ]);
    }

    /**
     * Calcula minutos trabalhados com base nos horários
     * 
     * @param \App\Models\TimeRecord $timeRecord
     * @return int Minutos trabalhados
     */
    private function calcularMinutosTrabalhados($timeRecord): int
    {
        if (!$timeRecord->entry_time || !$timeRecord->exit_time) {
            return 0;
        }

        $entrada = \Carbon\Carbon::parse($timeRecord->entry_time);
        $saida = \Carbon\Carbon::parse($timeRecord->exit_time);
        $totalMinutos = $saida->diffInMinutes($entrada);

        // Subtrai o tempo de almoço se houver
        if ($timeRecord->lunch_start && $timeRecord->lunch_end) {
            $almocoInicio = \Carbon\Carbon::parse($timeRecord->lunch_start);
            $almocoFim = \Carbon\Carbon::parse($timeRecord->lunch_end);
            $minutosAlmoco = $almocoFim->diffInMinutes($almocoInicio);
            $totalMinutos -= $minutosAlmoco;
        }

        return max(0, $totalMinutos);
    }
}