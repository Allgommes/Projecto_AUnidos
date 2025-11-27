# 🚀 APIs do AUnidos

Esta pasta contém todas as APIs RESTful do projeto AUnidos.

## 📁 Arquivos Disponíveis

| Arquivo | Descrição | Métodos |
|---------|-----------|---------|
| `test-connection.php` | Testa conexão com BD e retorna estatísticas | GET |
| `educadores.php` | CRUD completo de educadores | GET, POST, PUT, DELETE |
| `servicos.php` | CRUD completo de serviços | GET, POST, PUT, DELETE |
| `users.php` | Listagem de utilizadores | GET |
| `testar_todas_apis.ps1` | Script de teste automatizado | - |

---

## 🧪 Teste Rápido

### Executar Script Automatizado
```powershell
.\api\testar_todas_apis.ps1
```

### Teste Manual
```powershell
# Testar conexão
Invoke-RestMethod "http://localhost/Projecto_AUnidos/api/test-connection.php"

# Listar educadores
Invoke-RestMethod "http://localhost/Projecto_AUnidos/api/educadores.php"

# Listar serviços
Invoke-RestMethod "http://localhost/Projecto_AUnidos/api/servicos.php"

# Listar utilizadores
Invoke-RestMethod "http://localhost/Projecto_AUnidos/api/users.php"
```

---

## 📋 Documentação das APIs

### 1️⃣ Test Connection API

**Endpoint:** `GET /api/test-connection.php`

**Descrição:** Testa conexão com o banco e retorna estatísticas do sistema.

**Resposta:**
```json
{
  "success": true,
  "message": "Conexão com o banco de dados bem-sucedida",
  "data": {
    "total_utilizadores": 9,
    "total_educadores": 7,
    "total_donos": 2,
    "total_servicos": 0,
    "total_agendamentos": 0,
    "total_avaliacoes": 0,
    "database_name": "aunidos",
    "timestamp": "2025-11-26 21:03:18"
  }
}
```

---

### 2️⃣ Educadores API

**Base URL:** `/api/educadores.php`

#### GET - Listar Todos
```http
GET /api/educadores.php
```

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "utilizador_id": 5,
      "nome": "Maria Santos",
      "email": "maria@example.com",
      "distrito": "Porto",
      "anos_experiencia": 5,
      "biografia": "...",
      "certificacoes": "...",
      "avaliacao_media": 4.80,
      "especialidades": "Obediência Básica, Socialização"
    }
  ]
}
```

#### GET - Buscar por ID
```http
GET /api/educadores.php?id=1
```

#### GET - Buscar por Distrito
```http
GET /api/educadores.php?distrito=Lisboa
```

#### GET - Buscar por Especialidade
```http
GET /api/educadores.php?especialidade=Obediência Básica
```

#### POST - Criar Educador
```http
POST /api/educadores.php
Content-Type: application/json

{
  "utilizador_id": 5,
  "anos_experiencia": 7,
  "biografia": "Especialista em comportamento canino",
  "certificacoes": "APECA, Etologia Aplicada",
  "foto_perfil": "educador5.jpg"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Educador criado com sucesso",
  "data": {
    "id": 8,
    "utilizador_id": 5
  }
}
```

#### PUT - Atualizar Educador
```http
PUT /api/educadores.php
Content-Type: application/json

{
  "id": 1,
  "anos_experiencia": 8,
  "biografia": "Biografia atualizada",
  "certificacoes": "Novas certificações"
}
```

#### DELETE - Remover Educador
```http
DELETE /api/educadores.php
Content-Type: application/json

{
  "id": 1
}
```

---

### 3️⃣ Serviços API

**Base URL:** `/api/servicos.php`

#### GET - Listar Todos
```http
GET /api/servicos.php
```

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "educador_id": 1,
      "nome": "Treino de Obediência Básica",
      "descricao": "Sessões de treino básico...",
      "preco_hora": 25.50,
      "duracao_minutos": 60,
      "ativo": true,
      "educador_nome": "Maria Santos",
      "distrito": "Porto"
    }
  ]
}
```

#### GET - Buscar por ID
```http
GET /api/servicos.php?id=1
```

#### GET - Buscar por Educador
```http
GET /api/servicos.php?educador_id=1
```

#### POST - Criar Serviço
```http
POST /api/servicos.php
Content-Type: application/json

{
  "educador_id": 1,
  "nome": "Treino de Obediência Básica",
  "descricao": "Sessões de treino básico para cães",
  "preco_hora": 25.50,
  "duracao_minutos": 60
}
```

**Validações:**
- `preco_hora` deve ser >= 0.01
- `duracao_minutos` deve ser >= 15
- Todos os campos são obrigatórios

**Resposta:**
```json
{
  "success": true,
  "message": "Serviço criado com sucesso",
  "data": {
    "id": 9,
    "educador_id": 1,
    "nome": "Treino de Obediência Básica"
  }
}
```

#### PUT - Atualizar Serviço
```http
PUT /api/servicos.php
Content-Type: application/json

{
  "id": 1,
  "nome": "Treino Avançado",
  "preco_hora": 30.00,
  "duracao_minutos": 90
}
```

#### DELETE - Remover Serviço
```http
DELETE /api/servicos.php
Content-Type: application/json

{
  "id": 1
}
```

---

### 4️⃣ Utilizadores API

**Base URL:** `/api/users.php`

#### GET - Listar Todos
```http
GET /api/users.php
```

**Resposta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "João Silva",
      "email": "joao@example.com",
      "telefone": "912345678",
      "distrito": "Lisboa",
      "tipo_utilizador": "dono",
      "ativo": true,
      "email_verificado": true,
      "data_criacao": "2025-11-26 10:00:00"
    }
  ],
  "total": 9
}
```

#### GET - Buscar por ID
```http
GET /api/users.php?id=1
```

**Resposta Adicional:** Inclui informações específicas do tipo:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nome": "Maria Santos",
    "tipo_utilizador": "educador",
    "educador_info": {
      "id": 3,
      "anos_experiencia": 5,
      "biografia": "...",
      "especialidades": "Obediência Básica, Socialização"
    }
  }
}
```

#### GET - Buscar por Tipo
```http
GET /api/users.php?tipo=educador
GET /api/users.php?tipo=dono
```

---

## 📊 Códigos de Status HTTP

| Código | Significado |
|--------|-------------|
| 200 | OK - Requisição bem-sucedida |
| 201 | Created - Recurso criado com sucesso |
| 400 | Bad Request - Dados inválidos ou incompletos |
| 404 | Not Found - Recurso não encontrado |
| 405 | Method Not Allowed - Método HTTP não permitido |
| 500 | Internal Server Error - Erro no servidor |

---

## 🔐 Segurança

✅ **Prepared Statements** - Todas as queries usam PDO com prepared statements  
✅ **CORS Habilitado** - APIs acessíveis via JavaScript  
✅ **Validação de Dados** - Inputs validados antes de processar  
✅ **JSON UTF-8** - Encoding correto para caracteres especiais  

---

## 🧪 Exemplos de Teste com PowerShell

### Criar um Serviço
```powershell
$servico = @{
    educador_id = 1
    nome = "Treino Básico"
    descricao = "Obediência básica para cães"
    preco_hora = 25.50
    duracao_minutos = 60
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" `
    -Method Post `
    -Body $servico `
    -ContentType "application/json; charset=utf-8"
```

### Atualizar um Serviço
```powershell
$update = @{
    id = 1
    preco_hora = 30.00
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" `
    -Method Put `
    -Body $update `
    -ContentType "application/json; charset=utf-8"
```

### Deletar um Serviço
```powershell
$delete = @{ id = 1 } | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" `
    -Method Delete `
    -Body $delete `
    -ContentType "application/json; charset=utf-8"
```

---

## 📝 Notas Importantes

1. **Encoding UTF-8**: Sempre use `charset=utf-8` no Content-Type para caracteres portugueses
2. **JSON Format**: Body deve ser JSON válido (use `ConvertTo-Json` no PowerShell)
3. **CORS**: APIs permitem requisições de qualquer origem (development only)
4. **Debug Mode**: Quando `DEBUG_MODE=true`, mensagens de erro detalhadas são retornadas

---

## 🚀 Próximos Passos

1. Importe a coleção do Postman: `postman/AUnidos_Collection.json`
2. Execute o script de teste: `.\api\testar_todas_apis.ps1`
3. Consulte `GUIA_POSTMAN.md` para mais detalhes

---

**Última atualização:** 26 Nov 2025  
**Versão:** 1.0
