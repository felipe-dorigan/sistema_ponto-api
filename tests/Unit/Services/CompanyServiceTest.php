<?php

namespace Tests\Unit\Services;

use App\Services\CompanyService;
use App\Repositories\CompanyRepository;
use App\Models\Company;
use App\DTO\CompanyDTO;
use App\Exceptions\CompanyNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Mockery;

class CompanyServiceTest extends TestCase
{
    private CompanyService $service;
    private CompanyRepository $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repositoryMock = Mockery::mock(CompanyRepository::class);
        $this->service = new CompanyService($this->repositoryMock);
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

    public function test_metodo_obter_deve_retornar_company_quando_encontrado(): void
    {
        // Arrange
        $id = 1;
        $expectedCompany = new Company();
        $expectedCompany->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($expectedCompany);

        // Act
        $result = $this->service->obter($id);

        // Assert
        $this->assertSame($expectedCompany, $result);
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
        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessage("Company com ID {$id} não encontrado");
        
        $this->service->obter($id);
    }

    public function test_metodo_incluir_deve_chamar_repositorio_com_dados_corretos(): void
    {
        // Arrange
        $dto = new CompanyDTO(
            'Teste Name',
            'Teste Cnpj',
            'Teste Email',
            'Teste Phone',
            'Descrição de teste',
            'Teste City',
            'Teste State',
            'Teste Zip_code',
            10,
            true
        );
        
        $expectedCompany = new Company();
        $expectedCompany->id = 1;
        
        $this->repositoryMock
            ->shouldReceive('incluir')
            ->once()
            ->with($dto->toArray())
            ->andReturn($expectedCompany);

        // Act
        $result = $this->service->incluir($dto);

        // Assert
        $this->assertSame($expectedCompany, $result);
    }

    public function test_metodo_atualizar_deve_atualizar_company_existente(): void
    {
        // Arrange
        $id = 1;
        $dto = new CompanyDTO(
            'Teste Name',
            'Teste Cnpj',
            'Teste Email',
            'Teste Phone',
            'Descrição de teste',
            'Teste City',
            'Teste State',
            'Teste Zip_code',
            10,
            true
        );
        
        $existingCompany = new Company();
        $existingCompany->id = $id;
        
        $updatedCompany = new Company();
        $updatedCompany->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingCompany);
            
        $this->repositoryMock
            ->shouldReceive('atualizar')
            ->once()
            ->with($id, Mockery::type('array'))
            ->andReturn($updatedCompany);

        // Act
        $result = $this->service->atualizar($id, $dto);

        // Assert
        $this->assertSame($updatedCompany, $result);
    }

    public function test_metodo_atualizar_deve_lancar_excecao_quando_company_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        $dto = new CompanyDTO(
            'Teste Name',
            'Teste Cnpj',
            'Teste Email',
            'Teste Phone',
            'Descrição de teste',
            'Teste City',
            'Teste State',
            'Teste Zip_code',
            10,
            true
        );
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessage("Company com ID {$id} não encontrado");
        
        $this->service->atualizar($id, $dto);
    }

    public function test_metodo_excluir_deve_excluir_company_existente(): void
    {
        // Arrange
        $id = 1;
        $existingCompany = new Company();
        $existingCompany->id = $id;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn($existingCompany);
            
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

    public function test_metodo_excluir_deve_lancar_excecao_quando_company_nao_encontrado(): void
    {
        // Arrange
        $id = 999;
        
        $this->repositoryMock
            ->shouldReceive('obterPorId')
            ->once()
            ->with($id)
            ->andReturn(null);

        // Act & Assert
        $this->expectException(CompanyNotFoundException::class);
        $this->expectExceptionMessage("Company com ID {$id} não encontrado");
        
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