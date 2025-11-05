# ✅ APIs FUNCIONANDO - Guia Rápido

## 🎉 SUCESSO! As APIs estão funcionando perfeitamente!

### 📊 Status Atual do Banco de Dados
- ✅ **5 utilizadores** cadastrados
- ✅ **11 educadores** com perfis
- ✅ **1 serviço** criado
- ✅ Conexão com MySQL funcionando

---

## 🚀 TESTES RÁPIDOS (PowerShell)

### 1️⃣ Testar Conexão
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/test-connection.php" -Method Get | ConvertTo-Json
```

### 2️⃣ Listar Utilizadores
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Get | ConvertTo-Json -Depth 5
```

### 3️⃣ Criar Novo Utilizador (Dono)
```powershell
$json = '{"nome":"Carlos Silva","email":"carlos@example.com","password":"senha123","tipo_utilizador":"dono","telefone":"911111111","distrito":"Lisboa"}' 
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

### 4️⃣ Criar Novo Educador
```powershell
$json = '{"nome":"Ana Costa","email":"ana@example.com","password":"senha123","tipo_utilizador":"educador","telefone":"922222222","distrito":"Coimbra"}' 
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

### 5️⃣ Criar Perfil de Educador (use o ID do passo 4)
```powershell
$json = '{"utilizador_id":19,"biografia":"Especialista em comportamento canino","anos_experiencia":10,"certificacoes":"Certificação XYZ","aprovado":true}' 
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/educadores.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

### 6️⃣ Criar Serviço (use o ID do educador do passo 5)
```powershell
$json = '{"educador_id":12,"nome":"Agility Iniciante","descricao":"Treino de agility para iniciantes","preco":40.00,"duracao_minutos":45}' 
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

### 7️⃣ Listar Educadores
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/educadores.php" -Method Get | ConvertTo-Json -Depth 5
```

### 8️⃣ Listar Serviços
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/servicos.php" -Method Get | ConvertTo-Json -Depth 5
```

### 9️⃣ Ver Utilizador Específico (ID 17)
```powershell
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php?id=17" -Method Get | ConvertTo-Json -Depth 5
```

### 🔟 Atualizar Utilizador (ID 17)
```powershell
$json = '{"nome":"João Silva Atualizado","telefone":"999888777"}' 
Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/api/users.php?id=17" -Method Put -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

---

## 📱 USANDO NO POSTMAN

### Configuração Inicial
1. Abra o Postman
2. Crie uma nova Collection chamada "AUnidos API"
3. Adicione os requests abaixo:

### Request 1: Testar Conexão
- **Método:** GET
- **URL:** `http://localhost/Projecto_AUnidos/api/test-connection.php`

### Request 2: Criar Utilizador
- **Método:** POST
- **URL:** `http://localhost/Projecto_AUnidos/api/users.php`
- **Headers:** 
  - `Content-Type`: `application/json`
- **Body (raw JSON):**
```json
{
    "nome": "Pedro Santos",
    "email": "pedro@example.com",
    "password": "senha123",
    "tipo_utilizador": "dono",
    "telefone": "933333333",
    "distrito": "Faro"
}
```

### Request 3: Listar Utilizadores
- **Método:** GET
- **URL:** `http://localhost/Projecto_AUnidos/api/users.php`

### Request 4: Criar Educador
- **Método:** POST
- **URL:** `http://localhost/Projecto_AUnidos/api/educadores.php`
- **Headers:** 
  - `Content-Type`: `application/json`
- **Body (raw JSON):**
```json
{
    "utilizador_id": 18,
    "biografia": "Educadora com 8 anos de experiência",
    "anos_experiencia": 8,
    "certificacoes": "Certificado ABC",
    "aprovado": true
}
```

### Request 5: Criar Serviço
- **Método:** POST
- **URL:** `http://localhost/Projecto_AUnidos/api/servicos.php`
- **Headers:** 
  - `Content-Type`: `application/json`
- **Body (raw JSON):**
```json
{
    "educador_id": 11,
    "nome": "Treino de Comportamento",
    "descricao": "Correção de problemas comportamentais",
    "preco": 70.00,
    "duracao_minutos": 90
}
```

### Request 6: Listar Serviços
- **Método:** GET
- **URL:** `http://localhost/Projecto_AUnidos/api/servicos.php`

---

## 🌐 ABRIR NO NAVEGADOR

### Ver JSON no Navegador
```
http://localhost/Projecto_AUnidos/api/test-connection.php
http://localhost/Projecto_AUnidos/api/users.php
http://localhost/Projecto_AUnidos/api/educadores.php
http://localhost/Projecto_AUnidos/api/servicos.php
```

---

## 📋 ESTRUTURA DOS DADOS

### Utilizador (User)
```json
{
    "nome": "Nome Completo",
    "email": "email@example.com",
    "password": "senha123",
    "tipo_utilizador": "dono" | "educador",
    "telefone": "912345678",
    "distrito": "Lisboa"
}
```

### Perfil de Educador
```json
{
    "utilizador_id": 1,
    "biografia": "Texto sobre o educador",
    "anos_experiencia": 5,
    "certificacoes": "Lista de certificados",
    "aprovado": true | false
}
```

### Serviço
```json
{
    "educador_id": 1,
    "nome": "Nome do Serviço",
    "descricao": "Descrição detalhada",
    "preco": 50.00,
    "duracao_minutos": 60
}
```

---

## ✅ CONFIRMADO - DADOS NO MYSQL

Os seguintes dados foram criados e estão no banco:

1. **Utilizador Dono (ID: 17)**
   - Nome: João Silva Teste
   - Email: joao.teste123@example.com
   - Distrito: Lisboa

2. **Utilizador Educador (ID: 18)**
   - Nome: Maria Educadora
   - Email: maria.edu@example.com
   - Distrito: Porto

3. **Perfil Educador (ID: 11)**
   - Utilizador: Maria Educadora
   - Experiência: 8 anos
   - Aprovado: Sim

4. **Serviço (ID: 11)**
   - Educador: Maria Educadora
   - Nome: Treino de Obediência Básica
   - Preço: €50.00
   - Duração: 60 minutos

---

## 🎯 ENDPOINTS DISPONÍVEIS

### APIs de Utilizadores (`/api/users.php`)
- ✅ `GET` - Listar todos
- ✅ `GET ?id=1` - Ver específico
- ✅ `POST` - Criar novo
- ✅ `PUT ?id=1` - Atualizar
- ✅ `DELETE ?id=1` - Deletar

### APIs de Educadores (`/api/educadores.php`)
- ✅ `GET` - Listar todos
- ✅ `GET ?id=1` - Ver específico
- ✅ `GET ?aprovado=1` - Filtrar aprovados
- ✅ `GET ?distrito=Lisboa` - Filtrar por distrito
- ✅ `POST` - Criar perfil
- ✅ `PUT ?id=1` - Atualizar

### APIs de Serviços (`/api/servicos.php`)
- ✅ `GET` - Listar todos
- ✅ `GET ?id=1` - Ver específico
- ✅ `GET ?educador_id=1` - Filtrar por educador
- ✅ `POST` - Criar serviço
- ✅ `PUT ?id=1` - Atualizar
- ✅ `DELETE ?id=1` - Desativar

### API de Teste (`/api/test-connection.php`)
- ✅ `GET` - Testar conexão e ver estatísticas

---

## 🔥 PRÓXIMOS PASSOS

Agora você pode:

1. ✅ **Criar mais dados de teste** usando as APIs
2. ✅ **Testar no Postman** com a collection
3. ✅ **Integrar com frontend** (React, Vue, etc)
4. ✅ **Desenvolver app mobile** consumindo estas APIs
5. ✅ **Criar testes automatizados**

---

## 📚 DOCUMENTAÇÃO COMPLETA

Para mais detalhes, consulte:
- `API_TESTS.md` - Documentação completa com todos os exemplos
- `SETUP.md` - Guia de instalação do projeto
- `MIGRATION.md` - Guia de atualização dos arquivos

---

**🎉 Parabéns! Suas APIs estão funcionando perfeitamente!** 🚀
