# CMS API - Sistema de Gerenciamento de Conteúdo

Uma API REST completa para gerenciamento de conteúdo com autenticação JWT, desenvolvida em Laravel 9.

## 🎯 Funcionalidades Principais

### ✅ Sistema de Autenticação
- **JWT Authentication** - Tokens seguros para autenticação
- **Registro/Login/Logout** - Fluxo completo de autenticação
- **Renovação de tokens** - Refresh automático de tokens
- **Middleware de proteção** - Rotas protegidas por autenticação

### ✅ Gerenciamento de Conteúdo
- **CRUD de Posts** - Criar, editar, visualizar e deletar posts
- **Sistema de Tags** - Categorização e organização de conteúdo
- **Comentários** - Interação dos usuários com o conteúdo
- **Busca e Filtros** - Buscar posts por título, conteúdo ou tags
- **Paginação** - Listagens paginadas para melhor performance

### ✅ Gerenciamento de Usuários
- **CRUD de Usuários** - Gerenciar usuários do sistema
- **Controle de acesso** - Usuários ativos/inativos
- **Perfis diferenciados** - Administradores e usuários comuns

### ✅ Dashboard e Estatísticas
- **Métricas do sistema** - Estatísticas gerais do CMS
- **Atividade recente** - Feed de atividades do sistema
- **Tags populares** - Ranking de tags mais utilizadas
- **Posts recentes** - Últimos posts criados

### ✅ Documentação Interativa
- **Swagger UI** - Documentação completa e interativa
- **Testes integrados** - Testar endpoints diretamente na interface
- **Exemplos de uso** - Request/Response para cada endpoint

## 🚀 Tecnologias Utilizadas

- **Laravel 9** - Framework PHP
- **PostgreSQL** - Banco de dados
- **JWT Auth** - Autenticação
- **Docker** - Containerização
- **Nginx** - Servidor web
- **PHP 8.1** - Linguagem de programação
- **L5-Swagger** - Documentação da API

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php      # Autenticação
│   │   ├── UserController.php      # Gerenciamento de usuários
│   │   ├── PostController.php      # Gerenciamento de posts
│   │   ├── TagController.php       # Gerenciamento de tags
│   │   └── DashboardController.php # Estatísticas e métricas
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Tag.php
│   │   └── Comment.php
│   ├── Repositories/               # Repository Pattern
│   └── Traits/                     # Traits para Request/Response
├── database/
│   ├── migrations/                 # Estrutura do banco
│   └── seeders/                    # Dados de exemplo
├── routes/
│   └── api.php                     # Rotas da API
└── docker-compose.yml              # Configuração Docker
```

## 🛠️ Instalação e Configuração

### Pré-requisitos
- Docker e Docker Compose
- Git

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd cms-api
```

### 2. Configure o ambiente
```bash
# Subir os containers
docker-compose up -d

# Instalar dependências
docker-compose exec app composer install

# Executar migrations
docker-compose exec app php artisan migrate

# Popular com dados de exemplo
docker-compose exec app php artisan db:seed --class=CmsSeeder
```

### 3. Acessar a aplicação
- **API**: http://localhost:8000
- **Documentação Swagger**: http://localhost:8000/api/documentation
- **pgAdmin**: http://localhost:8080

## 📚 Documentação da API

### Endpoints Principais

#### 🔐 Autenticação
- `POST /api/auth/login` - Login de usuário
- `POST /api/auth/register` - Registro de usuário
- `POST /api/auth/logout` - Logout (requer token)
- `GET /api/auth/me` - Dados do usuário (requer token)
- `POST /api/auth/refresh` - Renovar token (requer token)

#### 👥 Usuários (requer token)
- `GET /api/users` - Listar usuários
- `POST /api/users` - Criar usuário
- `GET /api/users/{id}` - Buscar usuário por ID
- `PUT /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Deletar usuário

#### 📝 Posts (requer token)
- `GET /api/posts` - Listar posts (com filtros)
- `POST /api/posts` - Criar post
- `GET /api/posts/{id}` - Buscar post por ID
- `PUT /api/posts/{id}` - Atualizar post
- `DELETE /api/posts/{id}` - Deletar post
- `POST /api/posts/{post}/comments` - Adicionar comentário
- `DELETE /api/posts/{post}/comments/{comment}` - Deletar comentário

#### 🏷️ Tags (requer token)
- `GET /api/tags` - Listar tags
- `POST /api/tags` - Criar tag
- `GET /api/tags/{id}` - Buscar tag por ID
- `PUT /api/tags/{id}` - Atualizar tag
- `DELETE /api/tags/{id}` - Deletar tag

#### 📊 Dashboard (requer token)
- `GET /api/dashboard/stats` - Estatísticas do sistema
- `GET /api/dashboard/activity` - Atividade recente

#### 🔧 Utilitários
- `GET /api/health` - Health check
- `GET /api/ping` - Ping

### Parâmetros de Filtro

#### Posts
- `?per_page=15` - Número de itens por página
- `?tag=nome-da-tag` - Filtrar por tag específica
- `?query=palavra-chave` - Buscar por título ou conteúdo

## 🔐 Autenticação

A API utiliza JWT (JSON Web Token) para autenticação:

1. **Registre-se** ou **faça login** para obter um token
2. **Inclua o token** no header: `Authorization: Bearer {seu-token}`
3. **Use o token** para acessar rotas protegidas

### Usuários de Exemplo
- **Admin**: `admin@cms.com` / `admin123`
- **Editor**: `editor@cms.com` / `editor123`
- **Usuário**: `maria@example.com` / `123456`

## 🎨 Características do CMS

### Para Administradores
- Gerenciar usuários do sistema
- Criar e editar posts
- Moderar comentários
- Organizar conteúdo por tags
- Visualizar estatísticas do sistema

### Para Usuários
- Registrar-se no sistema
- Comentar em posts
- Buscar conteúdo
- Navegar por categorias

## 🚀 Deploy

O sistema está configurado para deploy com Docker:

```bash
# Produção
docker-compose -f docker-compose.yml up -d

# Railway
docker-compose -f docker-compose.railway.yml up -d
```

## 📈 Performance

- **Paginação** em todas as listagens
- **Índices** no banco de dados
- **Cache** de views e rotas
- **Rate Limiting** configurado
- **CORS** configurado

## 🔧 Desenvolvimento

### Executar testes
```bash
docker-compose exec app php artisan test
```

### Limpar cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Regenerar documentação
```bash
docker-compose exec app php artisan l5-swagger:generate
```

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📞 Suporte

Para suporte, entre em contato através dos issues do GitHub ou email.
3. O token expira em 60 minutos por padrão

### Exemplo de uso:

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "joao.silva@example.com", "password": "senha123"}'

# Usar token em requisições protegidas
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer {seu-token}"
```

## 🗄️ Banco de Dados

### Estrutura das Tabelas

#### Users
- `id` - Chave primária
- `nome` - Nome do usuário
- `email` - Email único
- `password` - Senha criptografada
- `telefone` - Telefone (opcional)
- `is_valid` - Status do usuário
- `created_at`, `updated_at` - Timestamps

#### Posts
- `id` - Chave primária
- `title` - Título do post
- `content` - Conteúdo do post
- `author_id` - FK para users
- `created_at`, `updated_at` - Timestamps

#### Tags
- `id` - Chave primária
- `name` - Nome da tag (único)
- `created_at`, `updated_at` - Timestamps

#### Post_Tag (Tabela pivot)
- `id` - Chave primária
- `post_id` - FK para posts
- `tag_id` - FK para tags
- `created_at`, `updated_at` - Timestamps

## 🧪 Dados de Exemplo

O sistema vem com seeders que criam dados de exemplo:

### Usuários
- João Silva (joao.silva@example.com)
- Maria Oliveira (maria.oliveira@example.com)
- Pedro Souza (pedro.souza@example.com)
- Ana Costa (ana.costa@example.com)

### Posts
- Notion (com tags: organization, planning, collaboration, writing, calendar)
- json-server (com tags: api, json, schema, node, github, rest)
- fastify (com tags: web, framework, node, http2, https, localhost)

## 🔧 Comandos Úteis

```bash
# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f app

# Acessar container
docker-compose exec app bash

# Executar testes (quando implementados)
docker-compose exec app php artisan test

# Limpar cache
docker-compose exec app php artisan cache:clear

# Recriar banco de dados
docker-compose exec app php artisan migrate:fresh --seed
```

## 📝 Padrões Implementados

### Repository Pattern
- Interfaces para repositories
- Implementação concreta dos repositories
- Dependency Injection nos controllers

### Traits
- `ApiRequestTrait` - Validação e manipulação de requests
- `ApiResponseTrait` - Padronização de respostas da API

### Middleware
- `JwtMiddleware` - Autenticação JWT
- Rate limiting configurado

### Form Requests
- Validação específica para cada endpoint
- Mensagens de erro personalizadas

## 🚀 Deploy

Para fazer deploy em produção:

1. Configure as variáveis de ambiente para produção
2. Execute `php artisan config:cache`
3. Execute `php artisan route:cache`
4. Configure o servidor web (Nginx/Apache)
5. Configure SSL/HTTPS
6. Configure backup do banco de dados

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 📞 Suporte

Para suporte, entre em contato através do email: dev@example.com

---

**Desenvolvido com ❤️ usando Laravel e Docker**
