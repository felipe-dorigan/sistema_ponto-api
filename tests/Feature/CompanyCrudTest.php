<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyCrudTest extends TestCase
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
        Company::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/company');

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
            'name' => 'Teste Name',
            'cnpj' => 'Teste Cnpj',
            'email' => 'Teste Email',
            'phone' => 'Teste Phone',
            'address' => 'Descrição de teste',
            'city' => 'Teste City',
            'state' => 'Teste State',
            'zip_code' => 'Teste Zip_code',
            'max_users' => 10,
            'active' => true
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', $dados);

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
        $this->assertDatabaseHas('companies', [
            'id' => $response->json('data.id')
        ]);
    }

        public function test_deve_validar_dados_obrigatorios_na_atualizacao_de_registro(): void
    {
        // Arrange
        $dados = [];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/company', $dados);

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
        $company = Company::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/company/{$company->id}");

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
        $this->assertEquals($company->id, $response->json('data.id'));
    }

    public function test_deve_atualizar_registro(): void
    {
        // Arrange
        $company = Company::factory()->create();
        $dadosAtualizacao = [
            'name' => 'Teste Name',
            'cnpj' => 'Teste Cnpj',
            'email' => 'Teste Email',
            'phone' => 'Teste Phone',
            'address' => 'Descrição de teste',
            'city' => 'Teste City',
            'state' => 'Teste State',
            'zip_code' => 'Teste Zip_code',
            'max_users' => 10,
            'active' => true
        ];

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/company/{$company->id}", $dadosAtualizacao);

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
        $this->assertEquals($company->id, $response->json('data.id'));
    }

    public function test_deve_excluir_registro(): void
    {
        // Arrange
        $company = Company::factory()->create();

        // Act
        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/company/{$company->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertDatabaseMissing('companies', [
            'id' => $company->id
        ]);
    }

        public function test_deve_retornar_404_para_registro_inexistente(): void
    {
        // Act
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/company/999999');

        // Assert
        $response->assertStatus(404);
    }

    public function test_deve_retornar_401_quando_nao_autenticado(): void
    {
        // Act
        $response = $this->getJson('/api/company');

        // Assert
        $response->assertStatus(401);
    }
}