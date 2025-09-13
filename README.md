# CMS API - Sistema de Gerenciamento de Conteúdo

Uma API REST completa para gerenciamento de postagens com títulos, autores, conteúdos e tags, desenvolvida em Laravel 9 com autenticação JWT.

## 📋 Funcionalidades

### ✅ Requisitos Obrigatórios Implementados

- ✅ **API REST completa** com todas as rotas solicitadas
- ✅ **Autenticação JWT** com login, registro e logout
- ✅ **CRUD de Usuários** com validação e paginação
- ✅ **CRUD de Posts** com filtros por tag e busca por conteúdo
- ✅ **Banco de dados MySQL** com migrations
- ✅ **Seeders** para popular o banco com dados de exemplo
- ✅ **Dockerização** completa com docker-compose
- ✅ **Documentação Swagger/OpenAPI** completa
- ✅ **Repository Pattern** implementado
- ✅ **Traits** para Request e Response
- ✅ **Middleware** de autenticação JWT
- ✅ **Rate Limiting** configurado
- ✅ **Paginação** em todas as rotas GET
- ✅ **Dependency Injection** implementado

### 🎯 Funcionalidades Bônus

- ✅ **Validação robusta** com Form Requests
- ✅ **Tratamento de erros** padronizado
- ✅ **Relacionamentos** entre models bem estruturados
- ✅ **Scopes** para filtros e buscas
- ✅ **Estrutura de projeto** bem organizada

## 🚀 Tecnologias Utilizadas

- **Laravel 9** - Framework PHP
- **MySQL 8.0** - Banco de dados
- **JWT Auth** - Autenticação
- **Docker** - Containerização
- **Nginx** - Servidor web
- **PHP 8.1** - Linguagem de programação
- **Composer** - Gerenciador de dependências

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   └── PostController.php
│   │   ├── Middleware/
│   │   │   └── JwtMiddleware.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── RegisterRequest.php
│   │       ├── UserRequest.php
│   │       └── PostRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   └── Tag.php
│   ├── Repositories/
│   │   ├── UserRepositoryInterface.php
│   │   ├── UserRepository.php
│   │   ├── PostRepositoryInterface.php
│   │   └── PostRepository.php
│   └── Traits/
│       ├── ApiRequestTrait.php
│       └── ApiResponseTrait.php
├── database/
│   ├── migrations/
│   │   ├── create_users_table.php
│   │   ├── create_posts_table.php
│   │   ├── create_tags_table.php
│   │   └── create_post_tag_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── TagSeeder.php
│       └── PostSeeder.php
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── local.ini
├── routes/
│   └── api.php
├── docker-compose.yml
├── Dockerfile
├── swagger.yaml
└── README.md
```

## 🛠️ Instalação e Configuração

### Pré-requisitos

- Docker e Docker Compose instalados
- Git (para clonar o repositório)

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd cms-api
```

### 2. Configure as variáveis de ambiente

```bash
cp env.example .env
```

Edite o arquivo `.env` com suas configurações:

```env
APP_NAME="CMS API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cms_api
DB_USERNAME=cms_user
DB_PASSWORD=cms_password

JWT_SECRET=
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

### 3. Execute com Docker

```bash
# Subir os containers
docker-compose up -d

# Instalar dependências
docker-compose exec app composer install

# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Gerar chave JWT
docker-compose exec app php artisan jwt:secret

# Executar migrations
docker-compose exec app php artisan migrate

# Executar seeders
docker-compose exec app php artisan db:seed
```

### 4. Acessar a aplicação

- **API**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Documentação Swagger**: http://localhost:8000/api/documentation (se configurado)

## 📚 Documentação da API

A documentação completa da API está disponível no arquivo `swagger.yaml` e pode ser visualizada em ferramentas como Swagger UI ou Postman.

### Endpoints Principais

#### Autenticação (Sem autenticação necessária)
- `POST /api/auth/login` - Login de usuário
- `POST /api/auth/register` - Registro de usuário

#### Autenticação (Requer token JWT)
- `POST /api/auth/logout` - Logout de usuário

#### Usuários (Requer token JWT)
- `GET /api/users` - Listar usuários (com paginação)
- `POST /api/users` - Criar usuário
- `GET /api/users/{id}` - Buscar usuário por ID
- `PUT /api/users/{id}` - Atualizar usuário
- `DELETE /api/users/{id}` - Deletar usuário

#### Posts (Requer token JWT)
- `GET /api/posts` - Listar posts (com paginação e filtros)
- `POST /api/posts` - Criar post
- `GET /api/posts/{id}` - Buscar post por ID
- `PUT /api/posts/{id}` - Atualizar post
- `DELETE /api/posts/{id}` - Deletar post

### Filtros e Parâmetros

#### Posts
- `?per_page=15` - Número de itens por página
- `?tag=node` - Filtrar por tag específica
- `?query=palavra-chave` - Buscar por título ou conteúdo

#### Usuários
- `?per_page=15` - Número de itens por página

## 🔐 Autenticação

A API utiliza JWT (JSON Web Token) para autenticação. Para acessar rotas protegidas:

1. Faça login ou registro para obter um token
2. Inclua o token no header `Authorization: Bearer {seu-token}`
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
