# Sistema de Ponto - Controle de Banco de Horas

Sistema completo para controle de ponto e gestão de banco de horas de colaboradores.

## 🚀 Tecnologias

### Backend (API)
- **Laravel 8** - Framework PHP
- **PostgreSQL 15** - Banco de dados
- **Laravel Sanctum** - Autenticação via tokens
- **Docker** - Containerização

### Frontend (em desenvolvimento)
- **Next.js** - Framework React

## 📋 Funcionalidades

### Autenticação
- ✅ Registro de usuários
- ✅ Login/Logout com tokens
- ✅ Perfis: Admin e Usuário

### Registro de Ponto
- ✅ Registrar entrada, saída, início e fim de almoço
- ✅ Registro rápido (quick entry) com horário atual
- ✅ Visualizar histórico de pontos
- ✅ Cálculo automático de horas trabalhadas
- ✅ Cálculo de banco de horas (positivo/negativo)

### Gestão de Ausências
- ✅ Registrar ausências com motivo
- ✅ Aprovação/rejeição de ausências (Admin)
- ✅ Histórico de ausências

## 🐳 Instalação com Docker

### Pré-requisitos
- Docker
- Docker Compose

### Passo a passo

1. **Clone o repositório** (se ainda não foi feito)
```bash
cd c:\xampp7\htdocs\sistema_de_ponto
```

2. **O arquivo .env já está configurado** com as variáveis corretas para Docker.

3. **Inicie os containers**
```bash
docker-compose up -d
```

4. **Entre no container da aplicação**
```bash
docker exec -it sistema_ponto_app bash
```

5. **Instale as dependências**
```bash
composer install
```

6. **Gere a chave da aplicação**
```bash
php artisan key:generate
```

7. **Execute as migrations**
```bash
php artisan migrate
```

8. **Crie um usuário administrador (opcional)**
```bash
php artisan tinker
```
Dentro do tinker:
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@sistema.com',
    'password' => bcrypt('senha123'),
    'role' => 'admin',
    'daily_work_hours' => 8
]);
```

9. **Acesse a aplicação**
- API: http://localhost:8000
- Banco de dados: localhost:5433 (Docker) ou localhost:5432 (local)

## 📚 Endpoints da API

### Autenticação (Públicos)

#### Registrar usuário
```http
POST /api/register
Content-Type: application/json

{
  "name": "João Silva",
  "email": "joao@exemplo.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "daily_work_hours": 8
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "joao@exemplo.com",
  "password": "senha123"
}
```

### Rotas Protegidas (Requerem Token)

Todas as rotas abaixo requerem o header:
```
Authorization: Bearer {seu_token_aqui}
```

#### Obter usuário autenticado
```http
GET /api/me
```

#### Logout
```http
POST /api/logout
```

### Registros de Ponto

#### Listar registros
```http
GET /api/time-records?start_date=2024-11-01&end_date=2024-11-30
```

#### Criar/atualizar registro
```http
POST /api/time-records
Content-Type: application/json

{
  "date": "2024-11-25",
  "entry_time": "08:00",
  "exit_time": "17:00",
  "lunch_start": "12:00",
  "lunch_end": "13:00",
  "notes": "Dia normal de trabalho"
}
```

#### Registro rápido (marca o próximo horário)
```http
POST /api/time-records/quick-entry
```

#### Consultar banco de horas
```http
GET /api/hour-bank?start_date=2024-11-01&end_date=2024-11-30
```

### Ausências

#### Listar ausências
```http
GET /api/absences?status=pending
```

#### Registrar ausência
```http
POST /api/absences
Content-Type: application/json

{
  "date": "2024-11-26",
  "start_time": "14:00",
  "end_time": "17:00",
  "reason": "Consulta médica",
  "description": "Retorno do dentista"
}
```

#### Ver ausência específica
```http
GET /api/absences/{id}
```

### Rotas Admin

#### Listar todas as ausências
```http
GET /api/admin/absences?status=pending
```

#### Aprovar/rejeitar ausência
```http
PATCH /api/admin/absences/{id}/status
Content-Type: application/json

{
  "status": "approved"
}
```
Status possíveis: `approved`, `rejected`

## 🗄️ Estrutura do Banco de Dados

### Tabela: users
- id
- name
- email
- password
- role (admin, user)
- daily_work_hours (default: 8)
- timestamps

### Tabela: time_records
- id
- user_id
- date
- entry_time
- exit_time
- lunch_start
- lunch_end
- worked_minutes
- expected_minutes
- notes
- timestamps

### Tabela: absences
- id
- user_id
- date
- start_time
- end_time
- reason
- description
- status (pending, approved, rejected)
- approved_by
- approved_at
- timestamps

## 🔧 Comandos Úteis

### Parar containers
```bash
docker-compose down
```

### Ver logs
```bash
docker-compose logs -f
```

### Acessar PostgreSQL
```bash
docker exec -it sistema_ponto_db psql -U postgres -d sistema_ponto
# Senha: acesse
```

### Limpar banco de dados e recriar
```bash
docker exec -it sistema_ponto_app php artisan migrate:fresh
```

## 📱 Próximos Passos

1. **Frontend Next.js**
   - Criar projeto Next.js
   - Implementar autenticação
   - Telas de registro de ponto
   - Dashboard com banco de horas
   - Gestão de ausências

2. **Melhorias**
   - Relatórios em PDF
   - Notificações por email
   - Exportação de dados
   - Gráficos de produtividade

## 📝 Observações

- Os erros de lint mostrados são normais em um ambiente sem vendor instalado
- O Laravel Sanctum já está configurado para autenticação via tokens
- O sistema calcula automaticamente o banco de horas baseado nas horas esperadas do usuário
- Administradores podem ver e aprovar ausências de todos os usuários

## 🤝 Contribuindo

Sinta-se à vontade para contribuir com melhorias!

## 📄 Licença

Este projeto é de código aberto.
