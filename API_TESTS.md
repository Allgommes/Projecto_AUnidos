# 🧪 GUIA DE TESTES - APIs AUnidos

## 📋 Índice
1. [Testar Conexão](#1-testar-conexão)
2. [API de Utilizadores](#2-api-de-utilizadores)
3. [API de Educadores](#3-api-de-educadores)
4. [API de Serviços](#4-api-de-serviços)
5. [Exemplos com cURL](#5-exemplos-com-curl)
6. [Coleção Postman](#6-coleção-postman)

---

## 1. Testar Conexão

### 🔗 Endpoint
```
GET http://localhost/Projecto_AUnidos/api/test-connection.php
```

### ✅ Resposta Esperada
```json
{
    "success": true,
    "message": "Conexão com o banco de dados estabelecida com sucesso!",
    "database": "aunidos",
    "statistics": {
        "utilizadores": 0,
        "educadores": 0,
        "donos": 0,
        "servicos": 0,
        "agendamentos": 0,
        "especialidades": 5
    },
    "timestamp": "2025-11-05 14:30:00"
}
```

### 🧪 Teste no PowerShell
```powershell
# Método 1 - Navegador
Start-Process "http://localhost/Projecto_AUnidos/api/test-connection.php"

# Método 2 - PowerShell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/test-connection.php" -Method Get | ConvertTo-Json -Depth 5
```

---

## 2. API de Utilizadores

### Base URL
```
http://localhost/Projecto_AUnidos/api/users.php
```

### 📖 Listar Todos os Utilizadores
**Request:**
```
GET /api/users.php
GET /api/users.php?limit=5&offset=0
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Get | ConvertTo-Json -Depth 5
```

---

### 📖 Ver Utilizador Específico
**Request:**
```
GET /api/users.php?id=1
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php?id=1" -Method Get | ConvertTo-Json -Depth 5
```

---

### ➕ Criar Novo Utilizador (Dono)
**Request:**
```
POST /api/users.php
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "nome": "João Silva",
    "email": "joao@example.com",
    "password": "senha123",
    "tipo_utilizador": "dono",
    "telefone": "912345678",
    "distrito": "Lisboa"
}
```

**PowerShell:**
```powershell
$body = @{
    nome = "João Silva"
    email = "joao@example.com"
    password = "senha123"
    tipo_utilizador = "dono"
    telefone = "912345678"
    distrito = "Lisboa"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### ➕ Criar Novo Utilizador (Educador)
**Body (JSON):**
```json
{
    "nome": "Maria Santos",
    "email": "maria@example.com",
    "password": "senha123",
    "tipo_utilizador": "educador",
    "telefone": "918765432",
    "distrito": "Porto"
}
```

**PowerShell:**
```powershell
$body = @{
    nome = "Maria Santos"
    email = "maria@example.com"
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### ✏️ Atualizar Utilizador
**Request:**
```
PUT /api/users.php?id=1
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "nome": "João Silva Atualizado",
    "telefone": "919999999",
    "distrito": "Setúbal"
}
```

**PowerShell:**
```powershell
$body = @{
    nome = "João Silva Atualizado"
    telefone = "919999999"
    distrito = "Setúbal"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php?id=1" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### 🗑️ Deletar Utilizador
**Request:**
```
DELETE /api/users.php?id=1
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php?id=1" -Method Delete | ConvertTo-Json -Depth 5
```

---

## 3. API de Educadores

### Base URL
```
http://localhost/Projecto_AUnidos/api/educadores.php
```

### 📖 Listar Todos os Educadores
**Request:**
```
GET /api/educadores.php
GET /api/educadores.php?distrito=Lisboa
GET /api/educadores.php?aprovado=1
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/educadores.php" -Method Get | ConvertTo-Json -Depth 5
```

---

### ➕ Criar Perfil de Educador
**Request:**
```
POST /api/educadores.php
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "utilizador_id": 2,
    "biografia": "Educador canino com 5 anos de experiência",
    "anos_experiencia": 5,
    "certificacoes": "Certificado ABC, DEF",
    "aprovado": true
}
```

**PowerShell:**
```powershell
$body = @{
    utilizador_id = 2
    biografia = "Educador canino com 5 anos de experiência"
    anos_experiencia = 5
    certificacoes = "Certificado ABC, DEF"
    aprovado = $true
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/educadores.php" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### ✏️ Atualizar Educador
**Request:**
```
PUT /api/educadores.php?id=1
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "biografia": "Biografia atualizada com mais detalhes",
    "anos_experiencia": 6,
    "aprovado": true
}
```

**PowerShell:**
```powershell
$body = @{
    biografia = "Biografia atualizada com mais detalhes"
    anos_experiencia = 6
    aprovado = $true
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/educadores.php?id=1" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

## 4. API de Serviços

### Base URL
```
http://localhost/Projecto_AUnidos/api/servicos.php
```

### 📖 Listar Todos os Serviços
**Request:**
```
GET /api/servicos.php
GET /api/servicos.php?educador_id=1
GET /api/servicos.php?tipo_servico=individual
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" -Method Get | ConvertTo-Json -Depth 5
```

---

### ➕ Criar Novo Serviço
**Request:**
```
POST /api/servicos.php
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "educador_id": 1,
    "nome": "Treino de Obediência Básica",
    "descricao": "Treino completo de comandos básicos",
    "tipo_servico": "individual",
    "preco": 50.00,
    "duracao_estimada": "1 hora"
}
```

**PowerShell:**
```powershell
$body = @{
    educador_id = 1
    nome = "Treino de Obediência Básica"
    descricao = "Treino completo de comandos básicos"
    tipo_servico = "individual"
    preco = 50.00
    duracao_estimada = "1 hora"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" -Method Post -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### ✏️ Atualizar Serviço
**Request:**
```
PUT /api/servicos.php?id=1
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "nome": "Treino de Obediência Avançada",
    "preco": 60.00,
    "duracao_estimada": "1.5 horas"
}
```

**PowerShell:**
```powershell
$body = @{
    nome = "Treino de Obediência Avançada"
    preco = 60.00
    duracao_estimada = "1.5 horas"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php?id=1" -Method Put -Body $body -ContentType "application/json" | ConvertTo-Json -Depth 5
```

---

### 🗑️ Desativar Serviço
**Request:**
```
DELETE /api/servicos.php?id=1
```

**PowerShell:**
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php?id=1" -Method Delete | ConvertTo-Json -Depth 5
```

---

## 5. Exemplos com cURL

### Testar Conexão
```bash
curl http://localhost/Projecto_AUnidos/api/test-connection.php
```

### Criar Utilizador
```bash
curl -X POST http://localhost/Projecto_AUnidos/api/users.php \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "Pedro Costa",
    "email": "pedro@example.com",
    "password": "senha123",
    "tipo_utilizador": "dono",
    "telefone": "911222333",
    "distrito": "Coimbra"
  }'
```

### Listar Utilizadores
```bash
curl http://localhost/Projecto_AUnidos/api/users.php
```

### Criar Serviço
```bash
curl -X POST http://localhost/Projecto_AUnidos/api/servicos.php \
  -H "Content-Type: application/json" \
  -d '{
    "educador_id": 1,
    "nome": "Agility - Nível Iniciante",
    "descricao": "Treino de agility para cães iniciantes",
    "tipo_servico": "grupo",
    "preco": 30.00,
    "duracao_estimada": "45 minutos"
  }'
```

---

## 6. Coleção Postman

### Importar para o Postman

1. **Abra o Postman**
2. **Clique em "Import"**
3. **Cole o JSON abaixo:**

```json
{
  "info": {
    "name": "AUnidos API",
    "description": "API para testes do projeto AUnidos",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Test Connection",
      "request": {
        "method": "GET",
        "header": [],
        "url": {
          "raw": "http://localhost/Projecto_AUnidos/api/test-connection.php",
          "protocol": "http",
          "host": ["localhost"],
          "path": ["Projecto_AUnidos", "api", "test-connection.php"]
        }
      }
    },
    {
      "name": "Users",
      "item": [
        {
          "name": "List Users",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/users.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "users.php"]
            }
          }
        },
        {
          "name": "Get User",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/users.php?id=1",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "users.php"],
              "query": [{"key": "id", "value": "1"}]
            }
          }
        },
        {
          "name": "Create User (Dono)",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"nome\": \"João Silva\",\n    \"email\": \"joao@example.com\",\n    \"password\": \"senha123\",\n    \"tipo_utilizador\": \"dono\",\n    \"telefone\": \"912345678\",\n    \"distrito\": \"Lisboa\"\n}"
            },
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/users.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "users.php"]
            }
          }
        },
        {
          "name": "Create User (Educador)",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"nome\": \"Maria Santos\",\n    \"email\": \"maria@example.com\",\n    \"password\": \"senha123\",\n    \"tipo_utilizador\": \"educador\",\n    \"telefone\": \"918765432\",\n    \"distrito\": \"Porto\"\n}"
            },
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/users.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "users.php"]
            }
          }
        },
        {
          "name": "Update User",
          "request": {
            "method": "PUT",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"nome\": \"João Silva Atualizado\",\n    \"telefone\": \"919999999\"\n}"
            },
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/users.php?id=1",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "users.php"],
              "query": [{"key": "id", "value": "1"}]
            }
          }
        }
      ]
    },
    {
      "name": "Educadores",
      "item": [
        {
          "name": "List Educadores",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/educadores.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "educadores.php"]
            }
          }
        },
        {
          "name": "Create Educador",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"utilizador_id\": 2,\n    \"biografia\": \"Educador canino com 5 anos de experiência\",\n    \"anos_experiencia\": 5,\n    \"certificacoes\": \"Certificado ABC\",\n    \"aprovado\": true\n}"
            },
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/educadores.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "educadores.php"]
            }
          }
        }
      ]
    },
    {
      "name": "Servicos",
      "item": [
        {
          "name": "List Servicos",
          "request": {
            "method": "GET",
            "header": [],
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/servicos.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "servicos.php"]
            }
          }
        },
        {
          "name": "Create Servico",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n    \"educador_id\": 1,\n    \"nome\": \"Treino de Obediência\",\n    \"descricao\": \"Treino básico de obediência\",\n    \"tipo_servico\": \"individual\",\n    \"preco\": 50.00,\n    \"duracao_estimada\": \"1 hora\"\n}"
            },
            "url": {
              "raw": "http://localhost/Projecto_AUnidos/api/servicos.php",
              "protocol": "http",
              "host": ["localhost"],
              "path": ["Projecto_AUnidos", "api", "servicos.php"]
            }
          }
        }
      ]
    }
  ]
}
```

---

## 🧪 SCRIPT DE TESTE COMPLETO

Salve e execute este script PowerShell para testar todas as APIs:

```powershell
# Script de teste completo
Write-Host "=== TESTANDO APIS AUNIDOS ===" -ForegroundColor Green

# 1. Testar conexão
Write-Host "`n1. Testando conexão..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/test-connection.php" -Method Get | ConvertTo-Json -Depth 5

# 2. Criar utilizador dono
Write-Host "`n2. Criando utilizador dono..." -ForegroundColor Yellow
$dono = @{
    nome = "João Silva"
    email = "joao.$(Get-Random)@example.com"
    password = "senha123"
    tipo_utilizador = "dono"
    telefone = "912345678"
    distrito = "Lisboa"
} | ConvertTo-Json
$result1 = Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $dono -ContentType "application/json"
$result1 | ConvertTo-Json -Depth 5

# 3. Criar utilizador educador
Write-Host "`n3. Criando utilizador educador..." -ForegroundColor Yellow
$educador = @{
    nome = "Maria Santos"
    email = "maria.$(Get-Random)@example.com"
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
} | ConvertTo-Json
$result2 = Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $educador -ContentType "application/json"
$result2 | ConvertTo-Json -Depth 5

# 4. Listar utilizadores
Write-Host "`n4. Listando utilizadores..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Get | ConvertTo-Json -Depth 5

Write-Host "`n=== TESTES CONCLUÍDOS ===" -ForegroundColor Green
```

---

## 📊 Códigos de Status HTTP

- **200** - OK (sucesso)
- **201** - Created (recurso criado)
- **400** - Bad Request (dados inválidos)
- **404** - Not Found (recurso não encontrado)
- **409** - Conflict (conflito, ex: email duplicado)
- **500** - Internal Server Error (erro no servidor)

---

## ✅ Checklist de Testes

- [ ] Testar conexão com banco (`test-connection.php`)
- [ ] Criar utilizador dono
- [ ] Criar utilizador educador
- [ ] Listar utilizadores
- [ ] Ver utilizador específico
- [ ] Atualizar utilizador
- [ ] Criar perfil de educador
- [ ] Listar educadores
- [ ] Criar serviço
- [ ] Listar serviços
- [ ] Atualizar serviço
- [ ] Desativar serviço

**Boa sorte nos testes! 🚀**
