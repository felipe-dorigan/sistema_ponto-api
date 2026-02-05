# 🚀 Sistema de Controle de Ponto API

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-336791?style=flat&logo=postgresql)](https://postgresql.org)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker)](https://docker.com)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen?style=flat)](#)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

> API RESTful moderna desenvolvida em Laravel com arquitetura em camadas, autenticação JWT, testes automatizados e ambiente totalmente containerizado.

## 🎯 Status do Projeto

**✅ PRODUÇÃO READY** - Todos os testes passando, CI/CD configurado, documentação completa

- ✅ **13 Testes** (100% passing)
- ✅ **Arquitetura Limpa** (Repository + Service + DTO)
- ✅ **JWT Auth** implementado e testado
- ✅ **Docker** environment completo
- ✅ **CI/CD Pipeline** GitHub Actions
- ✅ **Health Checks** e monitoring
- ✅ **Documentação** completa

## ✨ Características

- 🏗️ **Arquitetura em Camadas**: Repository Pattern, Service Layer e DTOs
- 🔐 **Autenticação JWT**: Sistema seguro com tokens JWT
- ✅ **Testes Completos**: 13 testes (unitários + integração) com 100% de cobertura
- 🐳 **Docker**: Ambiente completamente containerizado
- 📊 **Logging**: Sistema estruturado de logs com schema separado
- 🚀 **CI/CD**: Pipeline automatizado com GitHub Actions
- 🏥 **Health Checks**: Endpoints de monitoramento da aplicação

## 🛠️ Tecnologias Utilizadas

- **PHP 8.2+** - Linguagem moderna com tipagem forte
- **Laravel 10.x** - Framework robusto e elegante
- **PostgreSQL 15+** - Banco de dados relacional avançado
- **Nginx** - Servidor web de alta performance
- **Docker** - Containerização completa
- **Redis** - Cache e sessões
- **JWT** - Autenticação stateless
- **PHPUnit** - Testes automatizados

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter o [Docker](https://www.docker.com/get-started) e o [Docker Compose](https://docs.docker.com/compose/install/) instalados em sua máquina.

## 🏗️ Arquitetura

```
📱 Controllers (HTTP Layer)
    ↓ FormRequests (Validation)
    ↓ DTOs (Data Transfer)
🔧 Services (Business Logic)
    ↓ Repositories (Data Access)
💾 Models (Database Layer)
```

### Padrões Implementados

- **Repository Pattern**: Abstração da camada de dados
- **Service Layer**: Lógica de negócio isolada
- **DTO Pattern**: Transferência segura de dados
- **Form Request Validation**: Validação centralizada
- **Resource Pattern**: Serialização consistente
- **Exception Handling**: Tratamento estruturado de erros

## 🚀 Instalação Rápida

### 1. Clone o Repositório

```bash
git clone https://github.com/felipe-dorigan/sistema-ponto-api.git
cd sistema-ponto-api
```

### 2. Configure o Ambiente

```bash
# Copie o arquivo de ambiente
cp .env.example .env

# As configurações padrão já funcionam com Docker
```

### 3. Inicie os Containers

```bash
# Construa e inicie todos os serviços
docker-compose up --build -d

# Aguarde os containers ficarem prontos (~30 segundos)
```

### 4. Configure a Aplicação

```bash
# Instale as dependências
docker-compose exec app composer install

# Execute as migrations
docker-compose exec app php artisan migrate --force

# Execute as seeds (usuário padrão)
docker-compose exec app php artisan db:seed --force
```

### 5. ✅ Pronto!

- **API**: http://localhost:8080
- **Banco Dev**: localhost:5433 (postgres/postgres/acesse)
- **Banco Test**: localhost:5434 (postgres/postgres/acesse)

## 🧪 Testes

A aplicação inclui testes unitários e de integração para garantir a qualidade do código.

### 5. ✅ Pronto!

- **API**: http://localhost:8080
- **Banco Dev**: localhost:5433 (postgres/postgres/acesse)
- **Banco Test**: localhost:5434 (postgres/postgres/acesse)

## 📚 Uso da API

### Autenticação

A API utiliza autenticação JWT. Primeiro, faça login para obter o token:

````

### Testes por Categoria

```bash
# Apenas testes unitários (2 testes)
docker-compose exec app php artisan test --testsuite=Unit

# Apenas testes de integração (11 testes)
docker-compose exec app php artisan test --testsuite=Feature

# Teste específico
docker-compose exec app php artisan test tests/Feature/UserCrudTest.php

# Com relatório detalhado
docker-compose exec app php artisan test --verbose
````

### Configuração do Ambiente de Teste

O projeto possui ambiente de teste isolado:

- **Banco separado**: PostgreSQL na porta 5434
- **Migrations automáticas**: RefreshDatabase trait
- **Seeds de teste**: Dados limpos para cada teste
- **Mocking**: Testes unitários isolados

```bash
# Configurar ambiente de teste manualmente
./setup-tests.sh    # Linux/Mac
./setup-tests.bat   # Windows
```

## 📚 Uso da API

### Autenticação

A API utiliza autenticação JWT. Primeiro, faça login para obter o token:

```bash
# Login (obtém token JWT)
curl -X POST http://localhost:8080/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@example.com",
    "password": "03139596"
  }'
```

**Resposta:**

```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### Endpoints Principais

| Método   | Endpoint          | Descrição                  | Autenticação |
| -------- | ----------------- | -------------------------- | ------------ |
| `POST`   | `/api/login`      | Fazer login                | ❌           |
| `POST`   | `/api/logout`     | Fazer logout               | ✅           |
| `GET`    | `/api/me`         | Dados do usuário atual     | ✅           |
| `GET`    | `/api/users`      | Listar usuários (paginado) | ✅           |
| `POST`   | `/api/users`      | Criar usuário              | ✅           |
| `GET`    | `/api/users/{id}` | Obter usuário específico   | ✅           |
| `PUT`    | `/api/users/{id}` | Atualizar usuário          | ✅           |
| `DELETE` | `/api/users/{id}` | Excluir usuário            | ✅           |

### Exemplos de Uso

**Listar Usuários:**

```bash
curl -X GET http://localhost:8080/api/users \
  -H "Authorization: Bearer SEU_JWT_TOKEN"
```

**Criar Usuário:**

```bash
curl -X POST http://localhost:8080/api/users \
  -H "Authorization: Bearer SEU_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva",
    "email": "joao@exemplo.com",
    "password": "senhaSegura123",
    "password_confirmation": "senhaSegura123"
  }'
```

**Atualizar Usuário:**

```bash
curl -X PUT http://localhost:8080/api/users/1 \
  -H "Authorization: Bearer SEU_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João Silva Santos"
  }'
```

## 🐳 Comandos Úteis do Docker

```bash
# Parar todos os containers
docker-compose down

# Acessar o terminal do container da aplicação
docker-compose exec app bash

# Visualizar os logs dos containers
docker-compose logs -f

# Executar comandos do Artisan
docker-compose exec app php artisan <comando>

# Reiniciar apenas um serviço
docker-compose restart app

# Cache e otimizações
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 🧪 Testes

### Executar Todos os Testes

```bash
# Executar toda a suíte (13 testes)
docker-compose exec app php artisan test

# Com relatório detalhado
docker-compose exec app php artisan test --verbose
```

### Testes por Categoria

```bash
# Apenas testes unitários (2 testes)
docker-compose exec app php artisan test --testsuite=Unit

# Apenas testes de integração (11 testes)
docker-compose exec app php artisan test --testsuite=Feature

# Teste específico
docker-compose exec app php artisan test tests/Feature/UserCrudTest.php
```

### Configuração do Ambiente de Teste

O projeto possui ambiente de teste isolado:

- **Banco separado**: PostgreSQL na porta 5434
- **Migrations automáticas**: RefreshDatabase trait
- **Seeds de teste**: Dados limpos para cada teste
- **Mocking**: Testes unitários isolados

```bash
# Configurar ambiente de teste manualmente
./setup-tests.sh    # Linux/Mac
./setup-tests.bat   # Windows
```

## 🔧 Desenvolvimento

### ⚡ Comandos Personalizados

O projeto possui **3 comandos Artisan personalizados** que automatizam completamente a criação e remoção de estruturas MVC:

- `criar:migration-customizada` - Cria migrations padronizadas
- `gerar:estrutura` - Gera arquitetura completa (12 arquivos)
- `remover:estrutura` - Remove estruturas com rollback seguro

**Recursos:** Repository + Service + DTO + Tests + Rotas autenticadas + Rollback automático

📖 **Documentação completa:** [COMANDOS-PERSONALIZADOS.md](./COMANDOS-PERSONALIZADOS.md)

### Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/     # Controllers da API
│   ├── Requests/        # Form Request Validation
│   └── Resources/       # API Resources
├── Services/            # Lógica de negócio
├── Repositories/        # Camada de acesso a dados
├── DTO/                 # Data Transfer Objects
├── Models/              # Eloquent Models
└── Exceptions/          # Exceções customizadas

tests/
├── Unit/               # Testes unitários
└── Feature/           # Testes de integração

database/
├── migrations/        # Estrutura do banco
├── seeders/          # Dados iniciais
└── factories/        # Factories para testes
```

## 👥 Equipe

- **Desenvolvedor Principal**: [Felipe](https://github.com/felipe-dorigan)

---

<div align="center">

**Desenvolvido usando Laravel & Docker**

[📖 Documentação](https://laravel.com/docs) • [🐛 Reportar Bug](https://github.com/felipe-dorigan/sistema-ponto-api/issues) • [💡 Solicitar Feature](https://github.com/seu-usuario/sistema-ponto-api/issues)

</div>
````
