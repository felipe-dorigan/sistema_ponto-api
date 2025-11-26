<?php

namespace Tests\Unit\Services;

use App\Services\AbsenceService;
use App\Repositories\AbsenceRepository;
use App\Models\Absence;
use App\DTO\AbsenceDTO;
use App\Exceptions\AbsenceNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Mockery;

class AbsenceServiceTest extends TestCase
{
    private AbsenceService $service;
    private AbsenceRepository $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repositoryMock = Mockery::mock(AbsenceRepository::class);
        $this->service = new AbsenceService($this->repositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_metodo_listar_deve_chamar_o_repositorio_corretamente(): void
    {
        // Arrange
        $perPage = 15;
        $expectedResult = Mockery::mock(LengthAwarePaginator::class);
        
        $this->repositoryMock
            ->shouldReceive('listar')
            ->once()
            ->with($perPage)
            ->andReturn($expectedResult);

        // Act
        $result = $this->service->listar($perPage);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    public function test_metodo_obter_deve_retornar_absence_quando_encontrado(): void
    {
        // Arrange
        $id = 1;
        $expectedAbsence = new Absence();
        $expectedAbsence->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($expectedAbsence);

        // Act
        $result = $this->service->obter($id);

        // Assert
        $this->assertSame($expectedAbsence, $result);
    }

    public function test_metodo_obter_deve_lancar_excecao_quando_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(AbsenceNotFoundException::class);
        $this->expectExceptionMessage("Absence com ID {$id} não encontrado");
        
        $this->service->obter($id);
    }

    public function test_metodo_incluir_deve_chamar_repositorio_com_dados_corretos(): void
    {
        // Arrange
        $dto = new AbsenceDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'Teste Reason',
            'Descrição de teste',
            'valor_teste'
        );
        
        $expectedAbsence = new Absence();
        $expectedAbsence->id = 1;
        
        $this->repositoryMock
            ->shouldReceive('incluir')
            ->once()
            ->with($dto->toArray())
            ->andReturn($expectedAbsence);

        // Act
        $result = $this->service->incluir($dto);

        // Assert
        $this->assertSame($expectedAbsence, $result);
    }

    public function test_metodo_atualizar_deve_atualizar_absence_existente(): void
    {
        // Arrange
        $id = 1;
        $dto = new AbsenceDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'Teste Reason',
            'Descrição de teste',
            'valor_teste'
        );
        
        $existingAbsence = new Absence();
        $existingAbsence->id = $id;
        
        $updatedAbsence = new Absence();
        $updatedAbsence->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingAbsence);
            
        $this->repositoryMock
            ->shouldReceive('atualizar')
            ->once()
            ->with($id, Mockery::type('array'))
            ->andReturn($updatedAbsence);

        // Act
        $result = $this->service->atualizar($id, $dto);

        // Assert
        $this->assertSame($updatedAbsence, $result);
    }

    public function test_metodo_atualizar_deve_lancar_excecao_quando_absence_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        $dto = new AbsenceDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'Teste Reason',
            'Descrição de teste',
            'valor_teste'
        );
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(AbsenceNotFoundException::class);
        $this->expectExceptionMessage("Absence com ID {$id} não encontrado");
        
        $this->service->atualizar($id, $dto);
    }

    public function test_metodo_excluir_deve_excluir_absence_existente(): void
    {
        // Arrange
        $id = 1;
        $existingAbsence = new Absence();
        $existingAbsence->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingAbsence);
            
        $this->repositoryMock
            ->shouldReceive('excluir')
            ->once()
            ->with($id)
            ->andReturn(true);

        // Act
        $result = $this->service->excluir($id);

        // Assert
        $this->assertTrue($result);
    }

    public function test_metodo_excluir_deve_lancar_excecao_quando_absence_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(AbsenceNotFoundException::class);
        $this->expectExceptionMessage("Absence com ID {$id} não encontrado");
        
        $this->service->excluir($id);
    }

    public function test_metodo_buscar_por_deve_chamar_repositorio_corretamente(): void
    {
        // Arrange
        $campo = 'status';
        $valor = 'active';
        $expectedResult = collect();
        
        $this->repositoryMock
            ->shouldReceive('buscarPor')
            ->once()
            ->with($campo, $valor)
            ->andReturn($expectedResult);

        // Act
        $result = $this->service->buscarPor($campo, $valor);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    public function test_metodo_existe_deve_chamar_repositorio_corretamente(): void
    {
        // Arrange
        $campo = 'email';
        $valor = 'test@example.com';
        $excludeId = 1;
        
        $this->repositoryMock
            ->shouldReceive('existe')
            ->once()
            ->with($campo, $valor, $excludeId)
            ->andReturn(true);

        // Act
        $result = $this->service->existe($campo, $valor, $excludeId);

        // Assert
        $this->assertTrue($result);
    }
}