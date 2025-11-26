@echo off
REM Script para configurar e executar testes no Windows
REM Arquivo: setup-tests.bat

echo 🚀 Configurando ambiente de teste...

REM 1. Sobe os containers (incluindo o banco de teste)
echo 📦 Subindo containers...
docker-compose up -d postgres_test

REM Aguarda o banco estar pronto
echo ⏳ Aguardando banco de dados de teste ficar pronto...
timeout /t 10 /nobreak >nul

REM 2. Executa as migrations no banco de teste
echo 📋 Executando migrations no banco de teste...
docker-compose exec app php artisan migrate --env=testing --force

REM 3. Executa as seeds no banco de teste (opcional)
echo 🌱 Executando seeds no banco de teste...
docker-compose exec app php artisan db:seed --env=testing --force

echo ✅ Ambiente de teste configurado com sucesso!
echo.
echo Para executar os testes, use:
echo   docker-compose exec app php artisan test
echo.
echo Para executar testes específicos:
echo   docker-compose exec app php artisan test tests/Feature/UserCrudTest.php
echo   docker-compose exec app php artisan test tests/Unit/Services/UserServiceTest.php

pause