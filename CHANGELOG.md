# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### 🔧 Changed

-   **BREAKING**: Rotas públicas/guest removidas por questões de segurança
-   Nomenclatura de arquivos de rota alterada de snake_case para camelCase
-   Todas as APIs agora requerem autenticação JWT obrigatoriamente

### 🐛 Fixed

-   Corrigido bug na geração de rotas onde `gerar:estrutura` usava kebab-case e `remover:estrutura` procurava por snake_case
-   Comando `remover:estrutura` agora encontra corretamente os arquivos criados pelo `gerar:estrutura`

### ✨ Added

-   Novo padrão camelCase para arquivos de rota (ex: `tbModulo.php`, `tbGrupoUsuario.php`)
-   Rollback automático de migrations ao usar `remover:estrutura --with-migration`
-   Documentação atualizada com convenções de nomenclatura

### 📚 Documentation

-   Atualizado README.md com informações sobre comandos personalizados
-   Documentadas convenções de nomenclatura no COMANDOS-PERSONALIZADOS.md
-   Adicionadas seções sobre segurança e padrões de nomenclatura

## [1.0.0] - 2025-10-23

### ✨ Added

-   Sistema completo de comandos artisan personalizados
-   Comando `criar:migration-customizada` para migrations padronizadas
-   Comando `gerar:estrutura` para geração automática de arquitetura completa
-   Comando `remover:estrutura` para remoção segura de estruturas
-   Arquitetura em camadas (Repository + Service + DTO)
-   Autenticação JWT implementada
-   Testes automatizados completos (100% cobertura)
-   Ambiente Docker containerizado
-   CI/CD com GitHub Actions
-   Sistema de logs estruturado
-   Health checks e monitoring

### 🏗️ Architecture

-   Repository Pattern implementado
-   Service Layer para lógica de negócio
-   DTO Pattern para transferência de dados
-   Exception Handling estruturado
-   Form Request Validation

### 🔒 Security

-   Autenticação JWT obrigatória
-   Middleware de autenticação em todas as rotas
-   Validação de dados estruturada
-   Rate limiting implementado

### 🧪 Testing

-   13 testes automatizados (unitários + integração)
-   Ambiente de teste isolado
-   RefreshDatabase para testes limpos
-   Mocking para testes unitários

### 📦 Infrastructure

-   Docker Compose completo
-   PostgreSQL 15+ como banco principal
-   Redis para cache e sessões
-   Nginx como proxy reverso
-   GitHub Actions para CI/CD
