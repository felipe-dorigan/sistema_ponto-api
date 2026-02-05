# Makefile para Sistema de Ponto API
# Facilita comandos comuns de desenvolvimento e deploy

.PHONY: help install start stop restart logs test build deploy clean

# Cores para output
YELLOW := \033[1;33m
GREEN := \033[0;32m
RED := \033[0;31m
NC := \033[0m

# Configurações
DOCKER_COMPOSE := docker-compose
DOCKER_COMPOSE_PROD := docker-compose -f docker-compose.prod.yml

## help: Mostra esta mensagem de ajuda
help:
	@echo "$(YELLOW)Sistema de Ponto API - Comandos Disponíveis:$(NC)"
	@echo ""
	@sed -n 's/^##//p' $(MAKEFILE_LIST) | column -t -s ':' | sed -e 's/^/ /'
	@echo ""

## install: Instala todas as dependências
install:
	@echo "$(GREEN)📦 Instalando dependências...$(NC)"
	$(DOCKER_COMPOSE) exec app composer install
	$(DOCKER_COMPOSE) exec app npm install
	@echo "$(GREEN)✅ Dependências instaladas!$(NC)"

## start: Inicia os containers de desenvolvimento
start:
	@echo "$(GREEN)🚀 Iniciando containers de desenvolvimento...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Containers iniciados!$(NC)"

## stop: Para todos os containers
stop:
	@echo "$(YELLOW)⏹️  Parando containers...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✅ Containers parados!$(NC)"

## restart: Reinicia os containers
restart: stop start

## logs: Mostra os logs dos containers
logs:
	$(DOCKER_COMPOSE) logs -f

## test: Executa todos os testes
test:
	@echo "$(GREEN)🧪 Executando testes...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan test
	@echo "$(GREEN)✅ Testes concluídos!$(NC)"

## test-coverage: Executa testes com coverage
test-coverage:
	@echo "$(GREEN)🧪 Executando testes com coverage...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan test --coverage
	@echo "$(GREEN)✅ Testes com coverage concluídos!$(NC)"

## migrate: Executa as migrações do banco
migrate:
	@echo "$(GREEN)🗄️  Executando migrações...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate
	@echo "$(GREEN)✅ Migrações concluídas!$(NC)"

## seed: Executa os seeders
seed:
	@echo "$(GREEN)🌱 Executando seeders...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan db:seed
	@echo "$(GREEN)✅ Seeders concluídos!$(NC)"

## fresh: Reseta o banco e executa migrações e seeders
fresh:
	@echo "$(GREEN)🔄 Resetando banco de dados...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate:fresh --seed
	@echo "$(GREEN)✅ Banco resetado!$(NC)"

## cache-clear: Limpa todos os caches
cache-clear:
	@echo "$(GREEN)🧹 Limpando caches...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear
	@echo "$(GREEN)✅ Caches limpos!$(NC)"

## optimize: Otimiza a aplicação para produção
optimize:
	@echo "$(GREEN)⚡ Otimizando aplicação...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache
	@echo "$(GREEN)✅ Aplicação otimizada!$(NC)"

## build: Constrói as imagens Docker
build:
	@echo "$(GREEN)🏗️  Construindo imagens...$(NC)"
	$(DOCKER_COMPOSE) build --no-cache
	@echo "$(GREEN)✅ Imagens construídas!$(NC)"

## build-prod: Constrói imagens para produção
build-prod:
	@echo "$(GREEN)🏗️  Construindo imagens de produção...$(NC)"
	$(DOCKER_COMPOSE_PROD) build --no-cache
	@echo "$(GREEN)✅ Imagens de produção construídas!$(NC)"

## deploy-staging: Deploy para ambiente de staging
deploy-staging:
	@echo "$(GREEN)🚀 Fazendo deploy para staging...$(NC)"
	./deploy.sh staging
	@echo "$(GREEN)✅ Deploy para staging concluído!$(NC)"

## deploy-prod: Deploy para ambiente de produção
deploy-prod:
	@echo "$(RED)🚀 Fazendo deploy para PRODUÇÃO...$(NC)"
	@echo "$(YELLOW)⚠️  Tem certeza? Pressione CTRL+C para cancelar ou ENTER para continuar$(NC)"
	@read dummy
	./deploy.sh production
	@echo "$(GREEN)✅ Deploy para produção concluído!$(NC)"

## shell: Acessa o shell do container da aplicação
shell:
	$(DOCKER_COMPOSE) exec app bash

## db-shell: Acessa o shell do PostgreSQL
db-shell:
	$(DOCKER_COMPOSE) exec postgres psql -U postgres -d sistema_ponto_db

## clean: Remove containers, volumes e imagens não utilizadas
clean:
	@echo "$(YELLOW)🧹 Limpando recursos Docker...$(NC)"
	docker system prune -af --volumes
	@echo "$(GREEN)✅ Limpeza concluída!$(NC)"

## health: Verifica a saúde da aplicação
health:
	@echo "$(GREEN)🏥 Verificando saúde da aplicação...$(NC)"
	curl -s http://localhost/api/health | jq '.' || echo "$(RED)❌ Aplicação não está respondendo$(NC)"

## logs-app: Mostra logs apenas da aplicação
logs-app:
	$(DOCKER_COMPOSE) logs -f app

## logs-db: Mostra logs do banco de dados
logs-db:
	$(DOCKER_COMPOSE) logs -f postgres

## backup-db: Cria backup do banco de dados
backup-db:
	@echo "$(GREEN)💾 Criando backup do banco...$(NC)"
	mkdir -p ./backups
	$(DOCKER_COMPOSE) exec -T postgres pg_dump -U postgres sistema_ponto_db > ./backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Backup criado em ./backups/$(NC)"

## restore-db: Restaura backup do banco (use: make restore-db FILE=backup.sql)
restore-db:
	@echo "$(GREEN)📥 Restaurando backup do banco...$(NC)"
	$(DOCKER_COMPOSE) exec -T postgres psql -U postgres -d sistema_ponto_db < $(FILE)
	@echo "$(GREEN)✅ Backup restaurado!$(NC)"