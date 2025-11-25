# Frontend - Sistema de Ponto (Next.js)

Instruções para criar o frontend em Next.js virão aqui.

## Comandos para criar o projeto Next.js

```bash
# Criar projeto Next.js com TypeScript
npx create-next-app@latest frontend --typescript --tailwind --app --import-alias "@/*"

# Entrar no diretório
cd frontend

# Instalar dependências adicionais
npm install axios date-fns react-hook-form @hookform/resolvers zod zustand

# Executar em desenvolvimento
npm run dev
```

O frontend estará disponível em: http://localhost:3000

## Recursos necessários

- [ ] Sistema de autenticação (login/registro)
- [ ] Dashboard com resumo do banco de horas
- [ ] Tela de registro de ponto
- [ ] Histórico de pontos
- [ ] Gestão de ausências
- [ ] Tela admin para aprovar ausências
- [ ] Relatórios e exportação

## Estrutura sugerida

```
frontend/
├── src/
│   ├── app/
│   │   ├── (auth)/
│   │   │   ├── login/
│   │   │   └── register/
│   │   ├── dashboard/
│   │   ├── pontos/
│   │   ├── ausencias/
│   │   └── admin/
│   ├── components/
│   ├── lib/
│   │   ├── api.ts
│   │   └── auth.ts
│   ├── hooks/
│   └── types/
└── public/
```
