<?php

namespace Tests\Feature;

use App\Models\TimeRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimeRecordCrudTest extends TestCase
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
        TimeRecord::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/time-record');

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
            'entry_time' => 'valor_teste',
            'exit_time' => 'valor_teste',
            'lunch_start' => 'valor_teste',
            'lunch_end' => 'valor_teste',
            'worked_minutes' => 10,
            'expected_minutes' => 10,
            'notes' => 'Descrição de teste',
            'entry_time_recorded_at' => 'valor_teste',
            'exit_time_recorded_at' => 'valor_teste',
            'lunch_start_recorded_at' => 'valor_teste',
            'lunch_end_recorded_at' => 'valor_teste'
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/time-record', $dados);

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
        $this->assertDatabaseHas('time_records', [
            'id' => $response->json('data.id')
        ]);
    }

        public function test_deve_validar_dados_obrigatorios_na_atualizacao_de_registro(): void
    {
        // Arrange
        $dados = [];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/time-record', $dados);

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
        $time_record = TimeRecord::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/time-record/{$time_record->id}");

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
        $this->assertEquals($time_record->id, $response->json('data.id'));
    }

    public function test_deve_atualizar_registro(): void
    {
        // Arrange
        $time_record = TimeRecord::factory()->create();
        $dadosAtualizacao = [
            'date' => 'valor_teste',
            'entry_time' => 'valor_teste',
            'exit_time' => 'valor_teste',
            'lunch_start' => 'valor_teste',
            'lunch_end' => 'valor_teste',
            'worked_minutes' => 10,
            'expected_minutes' => 10,
            'notes' => 'Descrição de teste',
            'entry_time_recorded_at' => 'valor_teste',
            'exit_time_recorded_at' => 'valor_teste',
            'lunch_start_recorded_at' => 'valor_teste',
            'lunch_end_recorded_at' => 'valor_teste'
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/time-record/{$time_record->id}", $dadosAtualizacao);

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
        $this->assertEquals($time_record->id, $response->json('data.id'));
    }

    public function test_deve_excluir_registro(): void
    {
        // Arrange
        $time_record = TimeRecord::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/time-record/{$time_record->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseMissing('time_records', [
            'id' => $time_record->id
        ]);
    }

        public function test_deve_retornar_404_para_registro_inexistente(): void
    {
        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/time-record/999999');

        // Assert
        $response->assertStatus(404);
    }

    public function test_deve_retornar_401_quando_nao_autenticado(): void
    {
        // Act
        $response = $this->getJson('/api/time-record');

        // Assert
        $response->assertStatus(401);
    }
}