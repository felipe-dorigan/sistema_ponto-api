#!/bin/bash

# Deploy Script para Transportadoras API
# Este script automatiza o processo de deploy da aplicação

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Este script deve ser executado no diretório raiz do projeto Laravel"
fi

# Configurações
ENVIRONMENT=${1:-production}
BACKUP_DIR="./backups/$(date +%Y%m%d_%H%M%S)"

log "🚀 Iniciando deploy para ambiente: $ENVIRONMENT"

# 1. Criar backup do banco de dados
log "📦 Criando backup do banco de dados..."
mkdir -p "$BACKUP_DIR"

if [ "$ENVIRONMENT" = "production" ]; then
    docker-compose -f docker-compose.prod.yml exec -T db pg_dump -U root transportadoras > "$BACKUP_DIR/database_backup.sql"
else
    docker-compose exec -T postgres pg_dump -U postgres transportadoras_db > "$BACKUP_DIR/database_backup.sql"
fi

# 2. Atualizar código do repositório
log "📥 Atualizando código do repositório..."
git fetch origin
git reset --hard origin/main

# 3. Instalar/atualizar dependências
log "📦 Instalando dependências do Composer..."
docker-compose exec app composer install --optimize-autoloader --no-dev

log "📦 Instalando dependências do NPM..."
docker-compose exec app npm ci --production

# 4. Compilar assets
log "🔨 Compilando assets..."
docker-compose exec app npm run production

# 5. Executar migrações
log "🗄️ Executando migrações do banco de dados..."
docker-compose exec app php artisan migrate --force

# 6. Limpar caches
log "🧹 Limpando caches..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# 7. Otimizar aplicação
log "⚡ Otimizando aplicação..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 8. Reiniciar serviços
log "🔄 Reiniciando serviços..."
if [ "$ENVIRONMENT" = "production" ]; then
    docker-compose -f docker-compose.prod.yml down
    docker-compose -f docker-compose.prod.yml up -d
else
    docker-compose restart app nginx queue
fi

# 9. Executar testes de saúde
log "🏥 Executando testes de saúde..."
sleep 10

if [ "$ENVIRONMENT" = "production" ]; then
    HEALTH_URL="https://api.transportadoras.com/health"
else
    HEALTH_URL="http://localhost/health"
fi

# Verificar se a aplicação está respondendo
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$HEALTH_URL" || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    log "✅ Deploy concluído com sucesso!"
    log "🌐 Aplicação está rodando em: $HEALTH_URL"
else
    warn "⚠️  Deploy concluído, mas aplicação pode não estar respondendo corretamente"
    warn "Status HTTP: $HTTP_STATUS"
    
    # Rollback em caso de falha crítica
    if [ "$HTTP_STATUS" = "000" ] || [ "$HTTP_STATUS" = "500" ]; then
        error "❌ Falha crítica detectada. Execute rollback se necessário."
    fi
fi

# 10. Limpeza
log "🧹 Executando limpeza..."
docker system prune -f

log "📊 Informações do deploy:"
echo "  - Ambiente: $ENVIRONMENT"
echo "  - Backup: $BACKUP_DIR"
echo "  - Commit: $(git rev-parse --short HEAD)"
echo "  - Data: $(date)"

log "🎉 Deploy finalizado!"