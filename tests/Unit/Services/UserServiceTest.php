<?php

namespace Tests\Unit\Services;

use App\DTO\UserDTO;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserRepository|MockInterface $userRepositoryMock;
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria um "Mock" (dublê) do UserRepository.
        // Ele não executará o código real, apenas simulará o comportamento.
        $this->userRepositoryMock = Mockery::mock(UserRepository::class);

        // Instancia o nosso UserService, injetando o Mock no lugar do repositório real.
        $this->userService = new UserService($this->userRepositoryMock);
    }

    /** @test */
    public function metodo_incluir_deve_chamar_o_repositorio_com_os_dados_corretos()
    {
        // 1. Cenário (Arrange)
        $userDto = new UserDTO(
            name: 'Teste Unitário',
            email: 'unit@test.com',
            password: 'password123'
        );

        // Prepara o Mock: primeiro esperamos a chamada para contar usuários
        $this->userRepositoryMock
            ->shouldReceive('contarUsuarios')
            ->once()
            ->andReturn(true); // Simula um retorno de sucesso

        // Depois esperamos que o método 'incluir' do repositório
        // seja chamado UMA VEZ com um array específico.
        $this->userRepositoryMock
            ->shouldReceive('incluir')
            ->once()
            ->with(Mockery::on(function ($dados) use ($userDto) {
                // Verificamos se os dados passados para o repositório estão corretos
                return $dados['name'] === $userDto->name &&
                    $dados['email'] === $userDto->email &&
                    !empty($dados['password']); // A senha deve ser passada (mesmo que o model a encripte)
            }))
            ->andReturn(new \App\Models\User([ // Retorna um modelo User como esperado
                'id' => 1,
                'name' => $userDto->name,
                'email' => $userDto->email,
            ]));

        // 2. Ação (Act)
        $this->userService->incluir($userDto);

        // 3. Verificação (Assert)
        // O próprio Mockery já faz a verificação. Se o método não for chamado
        // como esperado, o teste falhará.
        $this->assertTrue(true); // Apenas para o teste não ser marcado como "arriscado"
    }
}
