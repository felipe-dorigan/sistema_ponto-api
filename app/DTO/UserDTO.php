<?php

namespace App\DTO;

use App\Http\Requests\User\UserRequest;

class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null
    ) {
    }

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'] ?? '',
            email: $validated['email'] ?? '',
            password: $validated['password'] ?? null
        );
    }

    public static function fromUpdateRequest(array $validated): self
    {
        return new self(
            name: $validated['name'] ?? '',
            email: $validated['email'] ?? '',
            password: $validated['password'] ?? null
        );
    }
}