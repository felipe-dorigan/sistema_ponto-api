# Comandos Artisan Personalizados

Este documento descreve os comandos Artisan personalizados criados para automatizar o desenvolvimento de recursos na API de Gerenciamento de Transportadoras.

## Visão Geral

Os comandos personalizados foram criados para facilitar o desenvolvimento seguindo um padrão arquitetural consistente. Todos os comandos seguem as convenções do Laravel:

-   **Entidades**: Nome no singular (ex: `Produto`, `Servico`)
-   **Tabelas**: Nome no plural (ex: `produtos`, `servicos`)

## Comandos Disponíveis

### 1. criar:migration-customizada

Cria migrations padronizadas seguindo as convenções do Laravel.

**Uso:**

```bash
docker-compose exec app php artisan criar:migration-customizada {nome_entidade}
```

**Exemplo:**

```bash
docker-compose exec app php artisan criar:migration-customizada produto
```

**O que faz:**

-   Cria uma migration com nome padronizado: `create_{entidades}_table`
-   Inclui campos básicos: `id`, `timestamps`
-   Segue a convenção de nomenclatura do Laravel
-   Permite customização posterior dos campos

**Arquivo gerado:**

-   `database/migrations/{timestamp}_create_{entidades}_table.php`

---

### 2. gerar:estrutura

Gera toda a estrutura arquitetural completa baseada em migrations existentes.

**Uso:**

```bash
docker-compose exec app php artisan gerar:estrutura {nome_entidade}
```

**Exemplo:**

```bash
docker-compose exec app php artisan gerar:estrutura produto
```

**Pré-requisitos:**

-   Migration da entidade deve existir em `database/migrations/`
-   Migration deve seguir o padrão `create_{entidades}_table`

**O que faz:**
Gera 12 arquivos organizados em uma estrutura completa:

#### Modelos e Lógica de Negócio

-   **Model**: `app/Models/{Entidade}.php`
-   **Repository**: `app/Repositories/{Entidade}Repository.php`
-   **Service**: `app/Services/{Entidade}Service.php`
-   **DTO**: `app/DTO/{Entidade}DTO.php`

#### Controllers e Rotas

-   **Controller**: `app/Http/Controllers/{Entidade}Controller.php`
-   **Rotas Autenticadas**: `routes/auth/{entidadeCamelCase}.php`

> **🔒 Nota sobre Rotas**: Apenas rotas autenticadas são geradas. Todas as APIs requerem autenticação JWT para maior segurança.

#### Tratamento de Exceções

-   **NotFoundException**: `app/Exceptions/{Entidade}NotFoundException.php`
-   **ValidationException**: `app/Exceptions/{Entidade}ValidationException.php`

#### Dados de Teste

-   **Factory**: `database/factories/{Entidade}Factory.php`

#### Testes Automatizados

-   **Feature Test**: `tests/Feature/{Entidade}CrudTest.php`
-   **Unit Test**: `tests/Unit/Services/{Entidade}ServiceTest.php`

**Funcionalidades Avançadas:**

-   **Análise automática da migration** para determinar campos e tipos
-   **Geração dinâmica de regras de validação** baseada nos campos da tabela
-   **Factory inteligente** com dados fake apropriados para cada tipo de campo
-   **Testes completos** cobrindo CRUD e lógica de negócio
-   **Tratamento de relacionamentos** (chaves estrangeiras)
-   **Validação de nomenclatura** seguindo convenções Laravel
-   **Nomenclatura camelCase** para arquivos de rota (ex: `tbModulo.php`, `tbGrupoUsuario.php`)

---

### 3. remover:estrutura

Remove completamente toda a estrutura gerada para uma entidade.

**Uso:**

```bash
php artisan remover:estrutura {nome_entidade} [--with-migration]
```

**Exemplo:**

```bash
# Remove apenas a estrutura (mantém migration)
php artisan remover:estrutura produto

# Remove estrutura + migration
php artisan remover:estrutura produto --with-migration
```

**O que faz:**

-   Remove todos os 12 arquivos gerados pelo comando `gerar:estrutura`
-   Opcionalmente remove a migration com `--with-migration`
-   **Rollback automático**: Faz rollback da migration antes de removê-la (evita tabelas órfãs)
-   Remove diretórios vazios após a remoção
-   Confirma a ação antes de executar
-   Exibe relatório detalhado dos arquivos removidos

**Arquivos removidos:**

-   Todos os arquivos listados no comando `gerar:estrutura`
-   Migration (apenas com `--with-migration`) - **com rollback automático**

## Fluxo de Desenvolvimento Recomendado

### 1. Criar Migration

```bash
php artisan criar:migration-customizada produto
```

### 2. Editar Migration

Edite o arquivo gerado para definir os campos específicos:

```php
Schema::create('produtos', function (Blueprint $table) {
    $table->id();
    $table->string('nome');
    $table->text('descricao')->nullable();
    $table->decimal('preco', 8, 2);
    $table->integer('estoque')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
});
```

### 3. Executar Migration

```bash
php artisan migrate
```

### 4. Gerar Estrutura Completa

```bash
php artisan gerar:estrutura produto
```

### 5. Personalizar (Opcional)

-   Ajustar regras de validação no Controller
-   Modificar lógica de negócio no Service
-   Personalizar testes conforme necessário

## Estrutura de Templates (Stubs)

Os comandos utilizam templates localizados em `stubs/`:

-   `auth-route.stub` - Rotas autenticadas
-   `controller.stub` - Controller com CRUD completo
-   `dto.stub` - Data Transfer Object
-   `factory.stub` - Factory para testes
-   `feature-test.stub` - Testes de funcionalidade
-   `guest-route.stub` - Rotas públicas
-   `model.stub` - Model Eloquent
-   `not-found-exception.stub` - Exceção de não encontrado
-   `repository.stub` - Repository pattern
-   `service.stub` - Service layer
-   `unit-test.stub` - Testes unitários
-   `validation-exception.stub` - Exceção de validação

## Convenções e Padrões

### Nomenclatura

-   **Entidades**: PascalCase singular (`Produto`, `Servico`)
-   **Tabelas**: snake_case plural (`produtos`, `servicos`)
-   **Arquivos**: Seguem padrão Laravel
-   **Rotas**: kebab-case (`/produtos`, `/servicos`)

### Arquitetura

-   **Repository Pattern**: Abstração de acesso a dados
-   **Service Layer**: Lógica de negócio centralizada
-   **DTO**: Transferência segura de dados
-   **Exception Handling**: Tratamento específico por entidade
-   **Testes**: Cobertura completa de funcionalidades

### Validação

-   Gerada automaticamente baseada nos campos da migration
-   Regras específicas por tipo de campo
-   Validação de chaves estrangeiras
-   Mensagens de erro em português

## Troubleshooting

### Migration não encontrada

**Erro**: "Migration não encontrada"
**Solução**: Certifique-se que existe uma migration com padrão `create_{entidades}_table`

### Arquivos já existem

**Erro**: Arquivos já existem
**Solução**: Use `remover:estrutura` antes de gerar novamente ou remova manualmente

### Permissões de arquivo

**Erro**: Erro de permissão
**Solução**: Verifique permissões de escrita nos diretórios de destino

## Exemplos Práticos

### Exemplo 1: Entidade Produto

```bash
# 1. Criar migration
php artisan criar:migration-customizada produto

# 2. Editar migration (adicionar campos específicos)
# 3. Executar migration
php artisan migrate

# 4. Gerar estrutura completa
php artisan gerar:estrutura produto

# 5. Executar testes
php artisan test --filter=Produto
```

### Exemplo 2: Entidade Cliente

```bash
# Fluxo completo
php artisan criar:migration-customizada cliente
# (editar migration)
php artisan migrate
php artisan gerar:estrutura cliente

# Testar API
# GET /api/clientes
# POST /api/clientes
# PUT /api/clientes/{id}
# DELETE /api/clientes/{id}
```

### Exemplo 3: Remoção Completa

```bash
# Remover tudo incluindo migration
php artisan remover:estrutura produto --with-migration
```

## Convenções de Nomenclatura

### Arquivos de Rota

Os arquivos de rota seguem o padrão **camelCase**:

-   **Modulo** → `modulo.php`
-   **GrupoUsuario** → `grupoUsuario.php`
-   **ModuloSistema** → `moduloSistema.php`

**Regra**: Primeira palavra minúscula, demais palavras com primeira letra maiúscula.

### Entidades e Classes

-   **Models**: PascalCase (ex: `Modulo`, `GrupoUsuario`)
-   **Controllers**: PascalCase + "Controller" (ex: `ModuloController`)
-   **Services**: PascalCase + "Service" (ex: `ModuloService`)
-   **Repositories**: PascalCase + "Repository" (ex: `ModuloRepository`)
-   **DTOs**: PascalCase + "DTO" (ex: `ModuloDTO`)

### Tabelas de Banco

-   Sempre no plural e snake_case (ex: `modulos`, `grupo_usuarios`)

### Segurança

-   **Rotas públicas**: Removidas por questões de segurança
-   **Apenas rotas autenticadas**: Todas as APIs requerem autenticação JWT
-   **Padrão**: `routes/auth/{entidadeCamelCase}.php`

## Benefícios

✅ **Consistência**: Todos os recursos seguem o mesmo padrão  
✅ **Produtividade**: Estrutura completa em segundos  
✅ **Qualidade**: Testes automatizados incluídos  
✅ **Manutenibilidade**: Código organizado e documentado  
✅ **Convenções Laravel**: Segue todas as boas práticas  
✅ **Português**: Interface e mensagens em português  
✅ **Flexibilidade**: Fácil personalização posterior

## Suporte

Para dúvidas ou problemas com os comandos personalizados, consulte:

-   Este documento
-   Código fonte em `app/Console/Commands/`
-   Templates em `stubs/`
-   Testes em `tests/Feature/` e `tests/Unit/`
