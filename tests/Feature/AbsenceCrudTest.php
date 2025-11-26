<?php

namespace Tests\Feature;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbsenceCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário para autenticação
        $this->user = User::factory()->create();
    }

    public function test_deve_listar_todos_os_registros_paginados(): void
    {
        // Arrange
        Absence::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/absence');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'first_page_url',
                    'last_page_url',
                    'next_page_url',
                    'prev_page_url',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertCount(5, $response->json('data.data'));
    }

    public function test_deve_criar_novo_registro(): void
    {
        // Arrange
        $dados = [
            'date' => 'valor_teste',
            'start_time' => 'valor_teste',
            'end_time' => 'valor_teste',
            'reason' => 'Teste Reason',
            'description' => 'Descrição de teste',
            'approved_at' => 'valor_teste'
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/absence', $dados);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('absences', [
            'id' => $response->json('data.id')
        ]);
    }

        public function test_deve_validar_dados_obrigatorios_na_atualizacao_de_registro(): void
    {
        // Arrange
        $dados = [];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/absence', $dados);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);

        $this->assertFalse($response->json('success'));
    }

        public function test_deve_obter_registro_especifico(): void
    {
        // Arrange
        $absence = Absence::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/absence/{$absence->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($absence->id, $response->json('data.id'));
    }

    public function test_deve_atualizar_registro(): void
    {
        // Arrange
        $absence = Absence::factory()->create();
        $dadosAtualizacao = [
            'date' => 'valor_teste',
            'start_time' => 'valor_teste',
            'end_time' => 'valor_teste',
            'reason' => 'Teste Reason',
            'description' => 'Descrição de teste',
            'approved_at' => 'valor_teste'
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/absence/{$absence->id}", $dadosAtualizacao);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($absence->id, $response->json('data.id'));
    }

    public function test_deve_excluir_registro(): void
    {
        // Arrange
        $absence = Absence::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/absence/{$absence->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseMissing('absences', [
            'id' => $absence->id
        ]);
    }

        public function test_deve_retornar_404_para_registro_inexistente(): void
    {
        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/absence/999999');

        // Assert
        $response->assertStatus(404);
    }

    public function test_deve_retornar_401_quando_nao_autenticado(): void
    {
        // Act
        $response = $this->getJson('/api/absence');

        // Assert
        $response->assertStatus(401);
    }
}