<?php

namespace App\Services;

use App\Models\TimeRecord;
use App\Repositories\TimeRecordRepository;
use App\DTO\TimeRecordDTO;
use App\Exceptions\TimeRecordValidationException;
use App\Exceptions\TimeRecordNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Service responsável pela lógica de negócio de registros de ponto
 * 
 * Esta classe contém as regras de negócio e validações para operações
 * relacionadas aos registros de ponto dos funcionários.
 */
class TimeRecordService
{
    /**
     * Construtor do serviço
     * 
     * @param TimeRecordRepository $time_recordRepository Repository de registros de ponto injetado via DI
     */
    public function __construct(
        private TimeRecordRepository $time_recordRepository
    ) {}

    /**
     * Lista todos os registros de ponto com paginação
     * 
     * @param int $perPage Número de registros por página (padrão: 15)
     * @return LengthAwarePaginator Lista paginada de registros
     */
    public function listar(int $perPage = 15): LengthAwarePaginator
    {
        return $this->time_recordRepository->listar($perPage);
    }

    /**
     * Obtém um registro de ponto pelo ID
     * 
     * @param int $id ID do registro de ponto
     * @return TimeRecord Registro de ponto encontrado
     * @throws TimeRecordNotFoundException Se o registro não for encontrado
     */
    public function obter(int $id): TimeRecord
    {
        $time_record = $this->time_recordRepository->obterPorId($id);
        
        if (!$time_record) {
            throw new TimeRecordNotFoundException("TimeRecord com ID {$id} não encontrado");
        }
        
        return $time_record;
    }

    /**
     * Cria um novo registro de ponto
     * 
     * @param TimeRecordDTO $dto Objeto com os dados do registro
     * @return TimeRecord Registro de ponto criado
     */
    public function incluir(TimeRecordDTO $dto): TimeRecord
    {
        // Validações de negócio aqui
        // Exemplo:
        // if ($this->time_recordRepository->existe('email', $dto->email)) {
        //     throw new \InvalidArgumentException('Email já existe');
        // }

        $dados = $dto->toArray();
        
        return $this->time_recordRepository->incluir($dados);
    }

    /**
     * Atualiza um registro de ponto existente
     *
     * @param int $id ID do registro a ser atualizado
     * @param TimeRecordDTO $dto Objeto com os novos dados
     * @return TimeRecord Registro de ponto atualizado
     * @throws TimeRecordNotFoundException Se o registro não for encontrado
     * @throws TimeRecordValidationException Se houver erro na atualização
     */
    public function atualizar(int $id, TimeRecordDTO $dto): TimeRecord
    {
        // Verifica se o TimeRecord existe
        if (!$this->time_recordRepository->obterPorId($id)) {
            throw new TimeRecordNotFoundException("TimeRecord com ID {$id} não encontrado");
        }        // Validações de negócio aqui
        // Exemplo:
        // if ($dto->email && $this->time_recordRepository->existe('email', $dto->email, $id)) {
        //     throw new \InvalidArgumentException('Email já existe');
        // }

        $dados = array_filter($dto->toArray(), function ($value) {
            return $value !== null;
        });
        
        $time_recordAtualizado = $this->time_recordRepository->atualizar($id, $dados);
        
        if (!$time_recordAtualizado) {
            throw new TimeRecordValidationException("Erro ao atualizar TimeRecord com ID {$id}");
        }
        
        return $time_recordAtualizado;
    }

    /**
     * Exclui um TimeRecord
     *
     * @param int $id
     * @return bool
     * @throws TimeRecordNotFoundException
     */
    public function excluir(int $id): bool
    {
        // Verifica se o TimeRecord existe
        if (!$this->time_recordRepository->obterPorId($id)) {
            throw new TimeRecordNotFoundException("TimeRecord com ID {$id} não encontrado");
        }        // Validações de negócio para exclusão aqui
        // Exemplo:
        // if ($this->temDependencias($id)) {
        //     throw new \InvalidArgumentException('Não é possível excluir, há registros dependentes');
        // }

        return $this->time_recordRepository->excluir($id);
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
        return $this->time_recordRepository->buscarPor($campo, $valor);
    }

    /**
     * Verifica se um TimeRecord existe
     * 
     * @param string $campo
     * @param mixed $valor
     * @param int|null $excludeId
     * @return bool
     */
    public function existe(string $campo, $valor, ?int $excludeId = null): bool
    {
        return $this->time_recordRepository->existe($campo, $valor, $excludeId);
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
        return $this->time_recordRepository->buscarPorUsuarioEPeriodo($userId, $dataInicio, $dataFim);
    }

    /**
     * Calcula o saldo total de horas de um usuário
     * 
     * @param int $userId
     * @return array
     */
    public function calcularSaldoHoras(int $userId): array
    {
        $registros = $this->time_recordRepository->buscarPor('user_id', $userId);
        
        $totalTrabalhado = 0;
        $totalEsperado = 0;
        
        foreach ($registros as $registro) {
            $totalTrabalhado += $registro->worked_minutes;
            $totalEsperado += $registro->expected_minutes;
        }
        
        $saldo = $totalTrabalhado - $totalEsperado;
        
        return [
            'total_trabalhado_minutos' => $totalTrabalhado,
            'total_esperado_minutos' => $totalEsperado,
            'saldo_minutos' => $saldo,
            'saldo_horas' => round($saldo / 60, 2),
            'status' => $saldo >= 0 ? 'positivo' : 'negativo'
        ];
    }

    /**
     * Obtém informações de auditoria de um registro
     * 
     * @param int $id
     * @return array
     */
    public function obterAuditoria(int $id): array
    {
        $registro = $this->obter($id);
        return $registro->getAuditInfo();
    }

    /**
     * Registra entrada do colaborador
     * 
     * @param int $userId
     * @param string $data
     * @param string $horario
     * @return TimeRecord
     */
    public function registrarEntrada(int $userId, string $data, string $horario): TimeRecord
    {
        $dto = new TimeRecordDTO(
            user_id: $userId,
            date: $data,
            entry_time: $horario,
            entry_time_recorded_at: now(),
            exit_time: null,
            lunch_start: null,
            lunch_end: null,
            worked_minutes: 0,
            expected_minutes: 480, // 8 horas padrão
            notes: null,
            exit_time_recorded_at: null,
            lunch_start_recorded_at: null,
            lunch_end_recorded_at: null
        );
        
        return $this->incluir($dto);
    }
}