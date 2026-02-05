<?php

namespace App\DTO;

/**
 * Data Transfer Object para registros de ponto
 * 
 * Este DTO encapsula os dados de registros de ponto transportados entre camadas,
 * garantindo imutabilidade e type-safety.
 */
class TimeRecordDTO
{
    public readonly int $user_id;
    public readonly string $date;
    public readonly ?string $entry_time;
    public readonly ?string $exit_time;
    public readonly ?string $lunch_start;
    public readonly ?string $lunch_end;
    public readonly int $worked_minutes;
    public readonly int $expected_minutes;
    public readonly ?string $notes;
    public readonly ?\Carbon\Carbon $entry_time_recorded_at;
    public readonly ?\Carbon\Carbon $exit_time_recorded_at;
    public readonly ?\Carbon\Carbon $lunch_start_recorded_at;
    public readonly ?\Carbon\Carbon $lunch_end_recorded_at;

    /**
     * Construtor do DTO
     * 
     * @param int $user_id ID do usuário/funcionário
     * @param string $date Data do registro de ponto
     * @param string|null $entry_time Horário de entrada
     * @param string|null $exit_time Horário de saída
     * @param string|null $lunch_start Início do almoço
     * @param string|null $lunch_end Fim do almoço
     * @param int $worked_minutes Minutos trabalhados
     * @param int $expected_minutes Minutos esperados (padrão: 480 = 8h)
     * @param string|null $notes Observações
     * @param \Carbon\Carbon|null $entry_time_recorded_at Timestamp de registro da entrada
     * @param \Carbon\Carbon|null $exit_time_recorded_at Timestamp de registro da saída
     * @param \Carbon\Carbon|null $lunch_start_recorded_at Timestamp de registro do início do almoço
     * @param \Carbon\Carbon|null $lunch_end_recorded_at Timestamp de registro do fim do almoço
     */
    public function __construct(
        int $user_id,
        string $date,
        ?string $entry_time = null,
        ?string $exit_time = null,
        ?string $lunch_start = null,
        ?string $lunch_end = null,
        int $worked_minutes = 0,
        int $expected_minutes = 480,
        ?string $notes = null,
        ?\Carbon\Carbon $entry_time_recorded_at = null,
        ?\Carbon\Carbon $exit_time_recorded_at = null,
        ?\Carbon\Carbon $lunch_start_recorded_at = null,
        ?\Carbon\Carbon $lunch_end_recorded_at = null
    ) {
        $this->user_id = $user_id;
        $this->date = $date;
        $this->entry_time = $entry_time;
        $this->exit_time = $exit_time;
        $this->lunch_start = $lunch_start;
        $this->lunch_end = $lunch_end;
        $this->worked_minutes = $worked_minutes;
        $this->expected_minutes = $expected_minutes;
        $this->notes = $notes;
        $this->entry_time_recorded_at = $entry_time_recorded_at;
        $this->exit_time_recorded_at = $exit_time_recorded_at;
        $this->lunch_start_recorded_at = $lunch_start_recorded_at;
        $this->lunch_end_recorded_at = $lunch_end_recorded_at;
    }

    /**
     * Cria uma instância do DTO a partir de um request ou array
     * 
     * @param array $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['date'],
            $data['entry_time'],
            $data['exit_time'],
            $data['lunch_start'],
            $data['lunch_end'],
            $data['worked_minutes'],
            $data['expected_minutes'],
            $data['notes'],
            $data['entry_time_recorded_at'],
            $data['exit_time_recorded_at'],
            $data['lunch_start_recorded_at'],
            $data['lunch_end_recorded_at']
        );
    }

    /**
     * Cria uma instância do DTO a partir de um array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return self::fromRequest($data);
    }

    /**
     * Converte o DTO para array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'date' => $this->date,
            'entry_time' => $this->entry_time,
            'exit_time' => $this->exit_time,
            'lunch_start' => $this->lunch_start,
            'lunch_end' => $this->lunch_end,
            'worked_minutes' => $this->worked_minutes,
            'expected_minutes' => $this->expected_minutes,
            'notes' => $this->notes,
            'entry_time_recorded_at' => $this->entry_time_recorded_at,
            'exit_time_recorded_at' => $this->exit_time_recorded_at,
            'lunch_start_recorded_at' => $this->lunch_start_recorded_at,
            'lunch_end_recorded_at' => $this->lunch_end_recorded_at
        ], function ($value) {
            return $value !== null;
        });
    }

    /**
     * Converte o DTO para JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Verifica se o DTO está válido
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        // Adicione validações específicas aqui
        // Exemplo:
        // return !empty($this->name) && !empty($this->email);
        return true;
    }

    /**
     * Obtém apenas os campos preenchidos
     * 
     * @return array
     */
    public function getFilledFields(): array
    {
        return array_filter($this->toArray(), function ($value) {
            return $value !== null && $value !== '';
        });
    }
}