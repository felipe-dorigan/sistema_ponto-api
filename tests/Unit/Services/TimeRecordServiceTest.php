<?php

namespace Tests\Unit\Services;

use App\Services\TimeRecordService;
use App\Repositories\TimeRecordRepository;
use App\Models\TimeRecord;
use App\DTO\TimeRecordDTO;
use App\Exceptions\TimeRecordNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Mockery;

class TimeRecordServiceTest extends TestCase
{
    private TimeRecordService $service;
    private TimeRecordRepository $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repositoryMock = Mockery::mock(TimeRecordRepository::class);
        $this->service = new TimeRecordService($this->repositoryMock);
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

    public function test_metodo_obter_deve_retornar_time_record_quando_encontrado(): void
    {
        // Arrange
        $id = 1;
        $expectedTimeRecord = new TimeRecord();
        $expectedTimeRecord->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($expectedTimeRecord);

        // Act
        $result = $this->service->obter($id);

        // Assert
        $this->assertSame($expectedTimeRecord, $result);
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
        $this->expectException(TimeRecordNotFoundException::class);
        $this->expectExceptionMessage("TimeRecord com ID {$id} não encontrado");
        
        $this->service->obter($id);
    }

    public function test_metodo_incluir_deve_chamar_repositorio_com_dados_corretos(): void
    {
        // Arrange
        $dto = new TimeRecordDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            10,
            10,
            'Descrição de teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste'
        );
        
        $expectedTimeRecord = new TimeRecord();
        $expectedTimeRecord->id = 1;
        
        $this->repositoryMock
            ->shouldReceive('incluir')
            ->once()
            ->with($dto->toArray())
            ->andReturn($expectedTimeRecord);

        // Act
        $result = $this->service->incluir($dto);

        // Assert
        $this->assertSame($expectedTimeRecord, $result);
    }

    public function test_metodo_atualizar_deve_atualizar_time_record_existente(): void
    {
        // Arrange
        $id = 1;
        $dto = new TimeRecordDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            10,
            10,
            'Descrição de teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste'
        );
        
        $existingTimeRecord = new TimeRecord();
        $existingTimeRecord->id = $id;
        
        $updatedTimeRecord = new TimeRecord();
        $updatedTimeRecord->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingTimeRecord);
            
        $this->repositoryMock
            ->shouldReceive('atualizar')
            ->once()
            ->with($id, Mockery::type('array'))
            ->andReturn($updatedTimeRecord);

        // Act
        $result = $this->service->atualizar($id, $dto);

        // Assert
        $this->assertSame($updatedTimeRecord, $result);
    }

    public function test_metodo_atualizar_deve_lancar_excecao_quando_time_record_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        $dto = new TimeRecordDTO(
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            10,
            10,
            'Descrição de teste',
            'valor_teste',
            'valor_teste',
            'valor_teste',
            'valor_teste'
        );
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(TimeRecordNotFoundException::class);
        $this->expectExceptionMessage("TimeRecord com ID {$id} não encontrado");
        
        $this->service->atualizar($id, $dto);
    }

    public function test_metodo_excluir_deve_excluir_time_record_existente(): void
    {
        // Arrange
        $id = 1;
        $existingTimeRecord = new TimeRecord();
        $existingTimeRecord->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingTimeRecord);
            
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

    public function test_metodo_excluir_deve_lancar_excecao_quando_time_record_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(TimeRecordNotFoundException::class);
        $this->expectExceptionMessage("TimeRecord com ID {$id} não encontrado");
        
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