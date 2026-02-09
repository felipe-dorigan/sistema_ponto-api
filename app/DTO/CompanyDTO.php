<?php

namespace App\DTO;

/**
 * Data Transfer Object para companys
 * 
 * Este DTO encapsula os dados de companys transportados entre camadas,
 * garantindo imutabilidade e type-safety.
 */
class CompanyDTO
{
    public readonly string $name;
    public readonly string $cnpj;
    public readonly string $email;
    public readonly string $phone;
    public readonly string $address;
    public readonly string $city;
    public readonly string $state;
    public readonly string $zip_code;
    public readonly int $max_users;
    public readonly bool $active;

    /**
     * Construtor do DTO
     * 
     * @param ... Documentar parâmetros de acordo com as propriedades
     */
    public function __construct(
        string $name,
        string $cnpj,
        string $email,
        string $phone,
        string $address,
        string $city,
        string $state,
        string $zip_code,
        int $max_users,
        bool $active
    ) {
        $this->name = $name;
        $this->cnpj = $cnpj;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zip_code = $zip_code;
        $this->max_users = $max_users;
        $this->active = $active;
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
            $data['name'],
            $data['cnpj'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['zip_code'],
            $data['max_users'],
            $data['active']
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
            'name' => $this->name,
            'cnpj' => $this->cnpj,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'max_users' => $this->max_users,
            'active' => $this->active
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