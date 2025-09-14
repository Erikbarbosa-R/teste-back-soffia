# 🚀 CMS API - Resumo Completo das Rotas

## 📋 **Informações Gerais**
- **Base URL Local:** `http://localhost:8080/api`
- **Base URL Railway:** `https://teste-back-soffia-production.up.railway.app/api`
- **Autenticação:** JWT Bearer Token
- **Content-Type:** `application/json`

---

## 🏥 **Health Check (Sem Autenticação)**

### `GET /health`
**Descrição:** Verifica se a API está funcionando
**Resposta:**
```json
{
  "status": "healthy"
}
```

### `GET /ping`
**Descrição:** Resposta simples de ping
**Resposta:**
```json
{
  "pong": true
}
```

---

## 🔐 **Autenticação (Sem JWT)**

### `POST /auth/login`
**Descrição:** Autentica usuário e retorna token JWT
**Body:**
```json
{
  "email": "admin@cms.com",
  "password": "password123"
}
```
**Resposta (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": "uuid",
    "nome": "Admin",
    "email": "admin@cms.com"
  }
}
```

### `POST /auth/register`
**Descrição:** Registra novo usuário
**Body:**
```json
{
  "nome": "João Silva",
  "email": "joao@email.com",
  "password": "password123",
  "telefone": "11999999999"
}
```
**Resposta (201):** Usuário criado com sucesso

---

## 🔒 **Autenticação (Com JWT - Header: `Authorization: Bearer {token}`)**

### `GET /auth/me`
**Descrição:** Retorna perfil do usuário autenticado
**Resposta (200):**
```json
{
  "id": "uuid",
  "nome": "João Silva",
  "email": "joao@email.com",
  "telefone": "11999999999",
  "is_valid": true,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z"
}
```

### `POST /auth/logout`
**Descrição:** Faz logout do usuário
**Resposta (200):** Logout realizado

### `POST /auth/refresh`
**Descrição:** Renova o token JWT
**Resposta (200):** Novo token gerado

---

## 👥 **Usuários (CRUD Completo - Com JWT)**

### `GET /users`
**Descrição:** Lista todos os usuários
**Resposta (200):**
```json
[
  {
    "id": "uuid",
    "nome": "João Silva",
    "email": "joao@email.com",
    "telefone": "11999999999",
    "is_valid": true,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
]
```

### `POST /users`
**Descrição:** Cria novo usuário
**Body:**
```json
{
  "nome": "Maria Santos",
  "email": "maria@email.com",
  "password": "password123",
  "telefone": "11888888888"
}
```
**Resposta (201):** Usuário criado

### `GET /users/{id}`
**Descrição:** Busca usuário específico
**Parâmetros:** `id` (UUID)
**Resposta (200):** Dados do usuário

### `PUT /users/{id}`
**Descrição:** Atualiza usuário
**Parâmetros:** `id` (UUID)
**Body:**
```json
{
  "nome": "João Silva Atualizado",
  "email": "joao.novo@email.com",
  "telefone": "11777777777"
}
```
**Resposta (200):** Usuário atualizado

### `DELETE /users/{id}`
**Descrição:** Remove usuário
**Parâmetros:** `id` (UUID)
**Resposta (204):** Usuário removido

---

## 📝 **Posts (CRUD Completo - Com JWT)**

### `GET /posts`
**Descrição:** Lista todos os posts
**Resposta (200):**
```json
[
  {
    "id": "uuid",
    "titulo": "Título do Post",
    "conteudo": "Conteúdo do post...",
    "autor_id": "uuid",
    "autor": {
      "id": "uuid",
      "nome": "João Silva"
    },
    "tags": [
      {
        "id": "uuid",
        "nome": "Tecnologia"
      }
    ],
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
]
```

### `POST /posts`
**Descrição:** Cria novo post
**Body:**
```json
{
  "titulo": "Meu Novo Post",
  "conteudo": "Conteúdo do meu post...",
  "tags": ["uuid-tag1", "uuid-tag2"]
}
```
**Resposta (201):** Post criado

### `GET /posts/{id}`
**Descrição:** Busca post específico
**Parâmetros:** `id` (UUID)
**Resposta (200):** Dados do post

### `PUT /posts/{id}`
**Descrição:** Atualiza post
**Parâmetros:** `id` (UUID)
**Body:**
```json
{
  "titulo": "Título Atualizado",
  "conteudo": "Conteúdo atualizado...",
  "tags": ["uuid-tag1"]
}
```
**Resposta (200):** Post atualizado

### `DELETE /posts/{id}`
**Descrição:** Remove post
**Parâmetros:** `id` (UUID)
**Resposta (204):** Post removido

### `POST /posts/{post_id}/comments`
**Descrição:** Adiciona comentário ao post
**Parâmetros:** `post_id` (UUID)
**Body:**
```json
{
  "content": "Excelente post! Muito informativo."
}
```
**Resposta (201):** Comentário adicionado

### `DELETE /posts/{post_id}/comments/{comment_id}`
**Descrição:** Remove comentário do post
**Parâmetros:** `post_id` (UUID), `comment_id` (UUID)
**Resposta (204):** Comentário removido

---

## 🏷️ **Tags (CRUD Completo - Com JWT)**

### `GET /tags`
**Descrição:** Lista todas as tags
**Resposta (200):**
```json
[
  {
    "id": "uuid",
    "nome": "Tecnologia",
    "cor": "#3498db",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
]
```

### `POST /tags`
**Descrição:** Cria nova tag
**Body:**
```json
{
  "nome": "JavaScript",
  "cor": "#f39c12"
}
```
**Resposta (201):** Tag criada

### `GET /tags/{id}`
**Descrição:** Busca tag específica
**Parâmetros:** `id` (UUID)
**Resposta (200):** Dados da tag

### `PUT /tags/{id}`
**Descrição:** Atualiza tag
**Parâmetros:** `id` (UUID)
**Body:**
```json
{
  "nome": "JavaScript Atualizado",
  "cor": "#e74c3c"
}
```
**Resposta (200):** Tag atualizada

### `DELETE /tags/{id}`
**Descrição:** Remove tag
**Parâmetros:** `id` (UUID)
**Resposta (204):** Tag removida

---

## 📊 **Dashboard (Com JWT)**

### `GET /dashboard/stats`
**Descrição:** Retorna estatísticas gerais
**Resposta (200):**
```json
{
  "total_users": 10,
  "total_posts": 25,
  "total_tags": 8,
  "total_comments": 45,
  "recent_posts": 5,
  "active_users": 7
}
```

### `GET /dashboard/activity`
**Descrição:** Retorna atividade recente
**Resposta (200):**
```json
[
  {
    "id": "uuid",
    "type": "post_created",
    "description": "João Silva criou um novo post",
    "created_at": "2023-01-01T00:00:00.000000Z"
  }
]
```

---

## 📖 **Documentação**

### `GET /documentation`
**Descrição:** Interface Swagger UI completa
**Resposta:** Página HTML com documentação interativa

### `GET /docs`
**Descrição:** Redireciona para documentação
**Resposta:** Redirecionamento para `/documentation`

---

## 🔧 **Exemplo de Integração Frontend**

### **Configuração Base:**
```javascript
const API_BASE = 'https://teste-back-soffia-production.up.railway.app/api';

// Função para fazer requisições
const apiRequest = async (endpoint, options = {}) => {
  const token = localStorage.getItem('jwt_token');
  
  const config = {
    headers: {
      'Content-Type': 'application/json',
      ...(token && { 'Authorization': `Bearer ${token}` })
    },
    ...options
  };
  
  const response = await fetch(`${API_BASE}${endpoint}`, config);
  return response.json();
};
```

### **Exemplos de Uso:**
```javascript
// Login
const login = async (email, password) => {
  const response = await apiRequest('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password })
  });
  
  if (response.access_token) {
    localStorage.setItem('jwt_token', response.access_token);
  }
  
  return response;
};

// Buscar posts
const getPosts = async () => {
  return await apiRequest('/posts');
};

// Criar post
const createPost = async (postData) => {
  return await apiRequest('/posts', {
    method: 'POST',
    body: JSON.stringify(postData)
  });
};

// Buscar usuário atual
const getCurrentUser = async () => {
  return await apiRequest('/auth/me');
};
```

---

## ⚠️ **Códigos de Erro Comuns**

- **401 Unauthorized:** Token JWT inválido ou expirado
- **403 Forbidden:** Usuário sem permissão
- **404 Not Found:** Recurso não encontrado
- **422 Unprocessable Entity:** Dados inválidos
- **500 Internal Server Error:** Erro interno do servidor

---

## 🚀 **Próximos Passos para Frontend**

1. **Configure a base URL** da API
2. **Implemente autenticação JWT** (login/logout)
3. **Crie serviços** para cada módulo (users, posts, tags)
4. **Implemente tratamento de erros**
5. **Adicione loading states** para melhor UX
6. **Configure interceptors** para renovação automática de token

**Todas as rotas estão funcionando e prontas para integração!** 🎉
