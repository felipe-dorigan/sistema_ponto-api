<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function deve_listar_todos_os_usuarios_paginados()
    {
        User::factory()->count(5)->create();
        $response = $this->actingAs($this->user, 'api')->getJson('/api/users');
        $response->assertStatus(200)->assertJsonCount(6, 'data');
    }

    /** @test */
    public function deve_incluir_um_novo_usuario_com_sucesso()
    {
        $userData = [
            'name' => 'Novo Usuário Teste',
            'email' => 'teste@exemplo.com',
            'password' => 'senhaSegura123',
            'password_confirmation' => 'senhaSegura123',
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/users', $userData);
        $response->assertStatus(201)->assertJsonFragment(['name' => 'Novo Usuário Teste']);
        $this->assertDatabaseHas('users', ['email' => 'teste@exemplo.com']);
    }

    /** @test */
    public function deve_falhar_ao_incluir_usuario_com_dados_invalidos()
    {
        $userData = [
            'name' => 'Usuário Inválido',
            'email' => 'email-invalido',
            'password' => '123',
        ];

        $response = $this->actingAs($this->user, 'api')->postJson('/api/users', $userData);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'email',
                    'password'
                ]
            ])
            ->assertJsonPath('data.email.0', 'The email must be a valid email address.')
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function deve_obter_um_usuario_especifico_por_codigo()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->user, 'api')->getJson("/api/users/{$user->id}");
        $response->assertStatus(200)->assertJsonFragment(['id' => $user->id, 'email' => $user->email]);
    }

    /** @test */
    public function deve_atualizar_um_usuario_existente()
    {
        $user = User::factory()->create();
        $updateData = ['name' => 'Nome Atualizado'];

        $response = $this->actingAs($this->user, 'api')->putJson("/api/users/{$user->id}", $updateData);
        $response->assertStatus(200)->assertJsonFragment(['name' => 'Nome Atualizado']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Atualizado']);
    }

    /** @test */
    public function deve_excluir_um_usuario()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->user, 'api')->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
