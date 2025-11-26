<?php

namespace Tests\Feature;

use App\Models\User;
use App\Exceptions\UserLimitExceededException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um usuário para autenticação nos testes
        $this->user = User::factory()->create();
    }

    /** @test */
    public function deve_retornar_erro_422_quando_dados_do_banco_sao_invalidos()
    {
        // Tenta criar usuário com email duplicado
        User::factory()->create(['email' => 'teste@exemplo.com']);

        $userData = [
            'name' => 'Usuário Duplicado',
            'email' => 'teste@exemplo.com', // Email já existe
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/users', $userData);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Os dados fornecidos são inválidos.']);
    }

    /** @test */
    public function deve_retornar_erro_404_quando_tentar_atualizar_usuario_inexistente()
    {
        $updateData = ['name' => 'Nome Atualizado'];

        $response = $this->actingAs($this->user, 'api')->putJson('/api/users/999', $updateData);

        $response->assertStatus(404);
    }

    /** @test */
    public function deve_retornar_erro_500_em_caso_de_falha_interna()
    {
        // Simula uma falha mockando o UserService
        $this->mock(\App\Services\UserService::class, function ($mock) {
            $mock->shouldReceive('listar')
                ->once()
                ->andThrow(new \Exception('Erro simulado'));
        });

        $response = $this->actingAs($this->user, 'api')->getJson('/api/users');

        $response->assertStatus(500)
            ->assertJsonFragment(['message' => 'Erro ao listar usuários.']);
    }

    /** @test */
    public function deve_logar_erros_sem_vazar_informacoes_sensíveis()
    {
        // Em ambiente de produção (APP_DEBUG=false), não deve mostrar detalhes
        config(['app.debug' => false]);

        $userData = [
            'name' => 'Usuário Teste',
            'email' => 'email-invalido', // Email inválido
            'password' => '123',
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/users', $userData);

        $response->assertStatus(422)
            ->assertJsonMissing(['trace']) // Não deve conter stack trace
            ->assertJsonMissing(['sql']); // Não deve conter queries SQL
    }
}