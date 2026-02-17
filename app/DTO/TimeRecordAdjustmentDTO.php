<?php

namespace App\DTO;

/**
 * Data Transfer Object para time-record-adjustments
 * 
 * Este DTO encapsula os dados de time-record-adjustments transportados entre camadas,
 * garantindo imutabilidade e type-safety.
 */
class TimeRecordAdjustmentDTO
{
    public readonly int $time_record_id;
    public readonly int $user_id;
    public readonly string $field_to_change;
    public readonly ?string $current_value;
    public readonly string $requested_value;
    public readonly string $reason;
    public readonly ?string $status;
    public readonly ?int $reviewed_by;
    public readonly ?string $reviewed_at;
    public readonly ?string $admin_notes;

    /**
     * Construtor do DTO
     * 
     * @param ... Documentar parâmetros de acordo com as propriedades
     */
    public function __construct(
        int $time_record_id,
        int $user_id,
        string $field_to_change,
        ?string $current_value,
        string $requested_value,
        string $reason,
        ?string $status = null,
        ?int $reviewed_by = null,
        ?string $reviewed_at = null,
        ?string $admin_notes = null
    ) {
        $this->time_record_id = $time_record_id;
        $this->user_id = $user_id;
        $this->field_to_change = $field_to_change;
        $this->current_value = $current_value;
        $this->requested_value = $requested_value;
        $this->reason = $reason;
        $this->status = $status;
        $this->reviewed_by = $reviewed_by;
        $this->reviewed_at = $reviewed_at;
        $this->admin_notes = $admin_notes;
    }

    /**
     * Cria uma instância do DTO a partir de dados validados
     * 
     * @param array $data Array de dados validados da requisição
     * @return self Nova instância do DTO
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['time_record_id'],
            $data['user_id'],
            $data['field_to_change'],
            $data['current_value'] ?? null,
            $data['requested_value'],
            $data['reason'],
            $data['status'] ?? null,
            $data['reviewed_by'] ?? null,
            $data['reviewed_at'] ?? null,
            $data['admin_notes'] ?? null
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
     * Converte o DTO para array (filtra valores nulos)
     * 
     * @return array Array com os dados do DTO
     */
    public function toArray(): array
    {
        return array_filter([
            'time_record_id' => $this->time_record_id,
            'user_id' => $this->user_id,
            'field_to_change' => $this->field_to_change,
            'current_value' => $this->current_value,
            'requested_value' => $this->requested_value,
            'reason' => $this->reason,
            'status' => $this->status,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at,
            'admin_notes' => $this->admin_notes
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