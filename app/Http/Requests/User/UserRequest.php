<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Se o método for POST, aplicamos as regras de CRIAÇÃO
        if ($this->isMethod('POST')) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ];
        }

        // Se o método for PUT ou PATCH, aplicamos as regras de ATUALIZAÇÃO
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $userId = $this->route('user')->id; // Pega o ID do usuário da URL

            return [
                'name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($userId), // Ignora o email do próprio usuário
                ],
                'password' => 'sometimes|nullable|string|min:8|confirmed', // Senha é opcional na atualização
            ];
        }

        return []; // Para outros métodos (GET, DELETE), não há regras de validação do corpo
    }
}
