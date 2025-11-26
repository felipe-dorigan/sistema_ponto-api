# 🚀 Guia de Deploy - Transportadoras API

## 📋 Pré-requisitos

-   **Docker** 20.10+
-   **Docker Compose** 2.0+
-   **Git** 2.30+
-   **Make** (opcional, mas recomendado)

## 🏗️ Ambientes Disponíveis

### Development (Desenvolvimento Local)

```bash
# Iniciar ambiente de desenvolvimento
make start

# Ou usando docker-compose diretamente
docker-compose up -d
```

### Staging (Homologação)

```bash
# Deploy automático para staging
make deploy-staging

# Ou usando script diretamente
./deploy.sh staging
```

### Production (Produção)

```bash
# Deploy para produção (requer confirmação)
make deploy-prod

# Ou usando script diretamente
./deploy.sh production
```

## 📦 Estrutura de Deploy

### 1. Configuração de Variáveis de Ambiente

Copie e configure o arquivo de ambiente:

```bash
cp .env.example .env
```

**Variáveis Críticas para Produção:**

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_32_CHARACTER_SECRET_KEY
DB_PASSWORD=STRONG_DATABASE_PASSWORD
JWT_SECRET=YOUR_JWT_SECRET_KEY
REDIS_PASSWORD=REDIS_PASSWORD
```

### 2. Geração de Chaves

```bash
# Gerar APP_KEY
php artisan key:generate

# Gerar JWT_SECRET
php artisan jwt:secret
```

### 3. Configuração do Banco de Dados

#### PostgreSQL em Produção

```yaml
# docker-compose.prod.yml
db:
    image: postgres:15-alpine
    environment:
        POSTGRES_DB: transportadoras
        POSTGRES_USER: postgres
        POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
        - postgres_data:/var/lib/postgresql/data
```

## 🔄 Pipeline de CI/CD

### GitHub Actions

O projeto inclui workflows automatizados:

1. **Tests Workflow** (`.github/workflows/tests.yml`)

    - Executa em: Push/PR para `main`
    - Testa: PHP 8.2, PostgreSQL 15
    - Inclui: Testes unitários, integração, coverage

2. **Deploy Workflow** (`.github/workflows/deploy.yml`)
    - Executa após: Testes passarem
    - Inclui: Build Docker, Security Scan, Deploy

### Configurar Secrets no GitHub

```bash
# Secrets necessários no repositório:
DOCKER_USERNAME=your_docker_username
DOCKER_PASSWORD=your_docker_password
```

## 🐳 Docker Images

### Development

```bash
# Build local
docker-compose build

# Pull da registry
docker pull dotplan/transportadoras-api:latest
```

### Production

```bash
# Build otimizada para produção
docker-compose -f docker-compose.prod.yml build

# Multi-stage build com otimizações
docker build -f docker/Dockerfile.prod -t transportadoras-api:prod .
```

## 🔧 Comandos Úteis

### Makefile Commands

```bash
make help           # Lista todos os comandos
make install        # Instala dependências
make test           # Executa testes
make migrate        # Executa migrações
make cache-clear    # Limpa caches
make health         # Verifica saúde da app
make backup-db      # Backup do banco
```

### Docker Commands

```bash
# Logs em tempo real
docker-compose logs -f

# Shell da aplicação
docker-compose exec app bash

# Shell do banco
docker-compose exec postgres psql -U postgres

# Restart específico
docker-compose restart app nginx
```

## 📊 Monitoramento

### Health Checks

#### Endpoint Principal

```bash
curl http://localhost:8080/api/health
```

**Resposta esperada:**

```json
{
    "success": true,
    "message": "Health check completed",
    "data": {
        "status": "healthy",
        "timestamp": "2025-01-15T10:30:00Z",
        "version": "1.0.0",
        "environment": "production",
        "checks": {
            "database": true,
            "cache": true,
            "storage": true
        },
        "uptime": {
            "seconds": 86400,
            "human": "1d 0h 0m 0s"
        }
    }
}
```

#### Ping (Load Balancers)

```bash
curl http://localhost:8080/api/ping
# Resposta: {"status":"ok"}
```

### Logs

```bash
# Logs da aplicação
docker-compose logs app

# Logs do nginx
docker-compose logs nginx

# Logs do banco
docker-compose logs postgres

# Logs específicos
tail -f storage/logs/laravel.log
```

## 🔒 Security Checklist

### Antes do Deploy em Produção

-   [ ] `APP_DEBUG=false`
-   [ ] `APP_ENV=production`
-   [ ] Chave JWT única e segura
-   [ ] Senhas do banco forte
-   [ ] SSL/TLS configurado
-   [ ] Firewall configurado
-   [ ] Backup automático configurado
-   [ ] Monitoramento configurado

### Configurações de Segurança

```env
# .env para produção
APP_DEBUG=false
LOG_LEVEL=error
DB_PASSWORD=complex_secure_password
JWT_SECRET=unique_jwt_secret_key
REDIS_PASSWORD=redis_secure_password
```

## 🗄️ Backup e Restore

### Backup Automático

```bash
# Backup manual
make backup-db

# Backup via script
./backup.sh daily
```

### Restore

```bash
# Restore de backup específico
make restore-db FILE=./backups/backup_20250115_103000.sql

# Restore via Docker
docker-compose exec -T postgres psql -U postgres -d transportadoras < backup.sql
```

## 🚨 Troubleshooting

### Problemas Comuns

#### 1. Container não inicia

```bash
# Verificar logs
docker-compose logs app

# Rebuild
docker-compose build --no-cache app
```

#### 2. Erro de permissões

```bash
# Corrigir permissões
sudo chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### 3. Banco não conecta

```bash
# Verificar se o container do PostgreSQL está rodando
docker-compose ps postgres

# Testar conexão
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo();
```

#### 4. Cache Redis não funciona

```bash
# Verificar Redis
docker-compose exec app php artisan cache:clear
redis-cli ping
```

### Rollback

Em caso de falha no deploy:

```bash
# Rollback usando Git
git checkout previous_stable_commit
./deploy.sh production

# Restore do banco se necessário
make restore-db FILE=./backups/pre_deploy_backup.sql
```

## 📞 Suporte

-   **Documentação**: [README.md](./README.md)
-   **Issues**: [GitHub Issues](https://github.com/seu-usuario/transportadoras-api/issues)
-   **CI/CD Status**: [GitHub Actions](https://github.com/seu-usuario/transportadoras-api/actions)

## 📈 Próximos Passos

1. Configurar monitoramento (Sentry, New Relic)
2. Implementar cache Redis distribuído
3. Configurar load balancer
4. Implementar backup automático
5. Configurar alertas de saúde
