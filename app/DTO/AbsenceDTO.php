<?php

namespace App\DTO;

class AbsenceDTO
{
    public readonly string $date;
    public readonly string $start_time;
    public readonly string $end_time;
    public readonly string $reason;
    public readonly string $description;
    public readonly string $approved_at;

    public function __construct(
        string $date,
        string $start_time,
        string $end_time,
        string $reason,
        string $description,
        string $approved_at
    ) {
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->reason = $reason;
        $this->description = $description;
        $this->approved_at = $approved_at;
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
            $data['start_time'],
            $data['end_time'],
            $data['reason'],
            $data['description'],
            $data['approved_at']
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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'reason' => $this->reason,
            'description' => $this->description,
            'approved_at' => $this->approved_at
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