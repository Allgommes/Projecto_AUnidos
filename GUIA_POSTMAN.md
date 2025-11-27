# 📬 Guia Completo de Testes com Postman - AUnidos

## 🚀 Importar Coleção no Postman

### Método 1: Importar JSON Diretamente
1. Abra o Postman
2. Clique em **"Import"** (canto superior esquerdo)
3. Selecione **"Upload Files"**
4. Navegue até `postman/AUnidos_Collection.json`
5. Clique em **"Import"**

### Método 2: Importar via URL
1. No Postman, clique em **"Import"**
2. Selecione a aba **"Link"**
3. Cole o caminho: `file://C:/xampp/htdocs/Projecto_AUnidos/postman/AUnidos_Collection.json`
4. Clique em **"Continue"** → **"Import"**

### Método 3: Copiar e Colar JSON
1. No Postman, clique em **"Import"**
2. Selecione a aba **"Raw text"**
3. Abra o arquivo `postman/AUnidos_Collection.json` e copie todo o conteúdo
4. Cole no campo de texto
5. Clique em **"Continue"** → **"Import"**

---

## 📋 Ordem de Testes Recomendada

### 1️⃣ **PRIMEIRO: Testar Conexão**
```http
GET http://localhost/Projecto_AUnidos/api/test-connection.php
```
✅ Deve retornar estatísticas do banco de dados

**Resposta Esperada:**
```json
{
  "success": true,
  "message": "Conexão com o banco de dados bem-sucedida",
  "data": {
    "total_utilizadores": 10,
    "total_educadores": 5,
    "total_donos": 5,
    "total_servicos": 8
  }
}
```

---

### 2️⃣ **Registar Utilizadores**

#### a) Registar Dono
```http
POST http://localhost/Projecto_AUnidos/register.php
Content-Type: application/json

{
  "nome": "João Silva",
  "email": "joao.silva@example.com",
  "password": "senha123",
  "tipo_utilizador": "dono",
  "telefone": "912345678",
  "distrito": "Lisboa"
}
```

**Resposta Esperada (Sucesso):**
```json
{
  "success": true,
  "message": "Conta criada com sucesso! Verifique seu email."
}
```

#### b) Registar Educador
```http
POST http://localhost/Projecto_AUnidos/register.php
Content-Type: application/json

{
  "nome": "Maria Santos",
  "email": "maria.santos@example.com",
  "password": "senha123",
  "tipo_utilizador": "educador",
  "telefone": "918765432",
  "distrito": "Porto",
  "anos_experiencia": 5,
  "biografia": "Educadora canina certificada com 5 anos de experiência.",
  "certificacoes": "Certificado APECA, Curso Etologia Canina"
}
```

---

### 3️⃣ **Verificar Email (Opcional)**
```http
GET http://localhost/Projecto_AUnidos/verify-email.php?token={{verification_token}}
```

⚠️ **Como obter o token:**
```sql
SELECT token_verificacao FROM utilizadores WHERE email = 'joao.silva@example.com';
```

**Ou via Postman:**
1. Execute o SQL acima no MySQL
2. Copie o token retornado
3. Substitua `{{verification_token}}` pelo valor copiado

---

### 4️⃣ **Fazer Login**
```http
POST http://localhost/Projecto_AUnidos/login.php
Content-Type: application/json

{
  "email": "joaquim@aunidos.com",
  "password": "password"
}
```

**Resposta Esperada (Sucesso):**
```json
{
  "success": true,
  "message": "Login realizado com sucesso!",
  "data": {
    "user_id": 1,
    "nome": "João Silva",
    "tipo_utilizador": "dono",
    "email_verificado": true
  }
}
```

---

### 5️⃣ **Recuperar Password**

#### a) Solicitar Reset
```http
POST http://localhost/Projecto_AUnidos/forgot-password.php
Content-Type: application/json

{
  "email": "joao.silva@example.com"
}
```

**Resposta Esperada:**
```json
{
  "success": true,
  "message": "Email enviado! Verifique sua caixa de entrada."
}
```

#### b) Reset Password
```http
POST http://localhost/Projecto_AUnidos/reset-password.php
Content-Type: application/json

{
  "token": "{{reset_token}}",
  "password": "novaSenha123",
  "confirm_password": "novaSenha123"
}
```

⚠️ **Como obter o reset_token:**
```sql
SELECT token_reset_password FROM utilizadores WHERE email = 'joao.silva@example.com';
```

---

## 🎯 Testes de API (Educadores e Serviços)

### **Educadores**

#### 1. Listar Todos Educadores
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php
```

**Resposta Esperada:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nome": "Maria Santos",
      "distrito": "Porto",
      "anos_experiencia": 5,
      "avaliacao_media": 4.8,
      "biografia": "Educadora canina certificada..."
    }
  ]
}
```

#### 2. Buscar Educador por ID
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?id=1
```

#### 3. Buscar por Distrito
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?distrito=Lisboa
```

#### 4. Buscar por Especialidade
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?especialidade=Obediência Básica
```

#### 5. Criar Educador
```http
POST http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "utilizador_id": 5,
  "anos_experiencia": 7,
  "biografia": "Especialista em comportamento canino",
  "certificacoes": "APECA, Etologia Aplicada",
  "foto_perfil": "educador5.jpg"
}
```

#### 6. Atualizar Educador
```http
PUT http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "id": 1,
  "anos_experiencia": 8,
  "biografia": "Biografia atualizada com mais experiência",
  "certificacoes": "APECA, Etologia Aplicada, Comportamento Animal"
}
```

#### 7. Deletar Educador
```http
DELETE http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "id": 1
}
```

---

### **Serviços**

#### 1. Listar Todos Serviços
```http
GET http://localhost/Projecto_AUnidos/api/servicos.php
```

**Resposta Esperada:**
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
      "ativo": true
    }
  ]
}
```

#### 2. Buscar Serviço por ID
```http
GET http://localhost/Projecto_AUnidos/api/servicos.php?id=1
```

#### 3. Criar Serviço
```http
POST http://localhost/Projecto_AUnidos/api/servicos.php
Content-Type: application/json

{
  "educador_id": 1,
  "nome": "Treino de Obediência Básica",
  "descricao": "Sessões de treino básico para cães de todas as idades",
  "preco_hora": 25.50,
  "duracao_minutos": 60
}
```

**Resposta Esperada:**
```json
{
  "success": true,
  "message": "Serviço criado com sucesso!",
  "data": {
    "id": 9,
    "educador_id": 1,
    "nome": "Treino de Obediência Básica"
  }
}
```

#### 4. Atualizar Serviço
```http
PUT http://localhost/Projecto_AUnidos/api/servicos.php
Content-Type: application/json

{
  "id": 1,
  "nome": "Treino de Obediência Avançada",
  "descricao": "Descrição atualizada do serviço",
  "preco_hora": 30.00,
  "duracao_minutos": 90
}
```

#### 5. Deletar Serviço
```http
DELETE http://localhost/Projecto_AUnidos/api/servicos.php
Content-Type: application/json

{
  "id": 1
}
```

---

## 🔧 Variáveis do Postman

A coleção já inclui estas variáveis que você pode usar nos requests:

| Variável | Descrição | Exemplo | Como Usar |
|----------|-----------|---------|-----------|
| `{{base_url}}` | URL base do projeto | `http://localhost/Projecto_AUnidos` | Automaticamente aplicada |
| `{{verification_token}}` | Token de verificação de email | (obtido do banco) | Preencher manualmente |
| `{{reset_token}}` | Token de reset de password | (obtido do banco) | Preencher manualmente |

### Como Configurar Variáveis:
1. No Postman, clique no ícone de "olho" (👁️) no canto superior direito
2. Clique em **"Edit"** ao lado de "AUnidos"
3. Edite os valores das variáveis
4. Salve

### Como Usar Variáveis nos Requests:
- Na URL: `{{base_url}}/register.php`
- No Body: `"token": "{{verification_token}}"`
- Nas Query Params: `?token={{reset_token}}`

---

## 📊 Respostas Esperadas

### ✅ Sucesso

#### 200 OK (GET, PUT)
```json
{
  "success": true,
  "message": "Operação realizada com sucesso",
  "data": { ... }
}
```

#### 201 Created (POST)
```json
{
  "success": true,
  "message": "Recurso criado com sucesso",
  "data": {
    "id": 1,
    ...
  }
}
```

### ❌ Erro

#### 400 Bad Request
```json
{
  "success": false,
  "error": "Dados inválidos ou incompletos"
}
```

#### 401 Unauthorized
```json
{
  "success": false,
  "error": "Credenciais inválidas"
}
```

#### 404 Not Found
```json
{
  "success": false,
  "error": "Recurso não encontrado"
}
```

#### 500 Internal Server Error
```json
{
  "success": false,
  "error": "Erro no servidor. Tente novamente."
}
```

---

## 🧪 Testes Automatizados (Scripts Postman)

Adicione estes scripts na aba **"Tests"** de cada requisição para validação automática:

### 1. Verificar Status 200
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});
```

### 2. Verificar JSON Válido
```javascript
pm.test("Response is JSON", function () {
    pm.response.to.be.json;
});
```

### 3. Verificar Success = true
```javascript
pm.test("Success is true", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.success).to.be.true;
});
```

### 4. Verificar Tempo de Resposta
```javascript
pm.test("Response time is less than 500ms", function () {
    pm.expect(pm.response.responseTime).to.be.below(500);
});
```

### 5. Salvar Token de Verificação (após registo)
```javascript
// Adicionar na aba Tests do request de registo
var jsonData = pm.response.json();
if (jsonData.data && jsonData.data.token) {
    pm.collectionVariables.set("verification_token", jsonData.data.token);
}
```

### 6. Salvar Reset Token (após forgot-password)
```javascript
var jsonData = pm.response.json();
if (jsonData.data && jsonData.data.reset_token) {
    pm.collectionVariables.set("reset_token", jsonData.data.reset_token);
}
```

### 7. Validar Estrutura de Dados
```javascript
pm.test("Response has required fields", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property('success');
    pm.expect(jsonData).to.have.property('data');
    pm.expect(jsonData.data).to.be.an('array');
});
```

---

## 🔍 Troubleshooting

### Erro: "Connection refused"
**Causa:** Apache não está rodando

**Solução:**
```powershell
# Verificar Apache
Get-Process | Where-Object {$_.Name -like "*httpd*"}

# Se não estiver rodando, inicie o XAMPP Control Panel
Start-Process "C:\xampp\xampp-control.exe"
```

---

### Erro: "404 Not Found"
**Causa:** URL incorreta ou arquivo não existe

**Solução:**
1. Verifique se a URL está correta (case-sensitive)
2. Confirme que o arquivo existe no diretório:
```powershell
Test-Path "C:\xampp\htdocs\Projecto_AUnidos\register.php"
```

---

### Erro: "500 Internal Server Error"
**Causa:** Erro no código PHP ou configuração

**Solução:**
```powershell
# Ver logs de erro do PHP
Get-Content C:\xampp\php\logs\php_error_log -Tail 20

# Ver logs de erro do Apache
Get-Content C:\xampp\apache\logs\error.log -Tail 20
```

---

### Erro: "Database connection failed"
**Causa:** MySQL não está rodando ou credenciais incorretas

**Solução:**
1. Verifique se o MySQL está rodando:
```powershell
Get-Process | Where-Object {$_.Name -like "*mysqld*"}
```

2. Verifique o arquivo `.env`:
```env
DB_HOST=localhost
DB_NAME=aunidos
DB_USER=root
DB_PASS=
```

3. Teste a conexão:
```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "SHOW DATABASES;"
```

---

### Erro: "Email not sent"
**Causa:** SMTP não configurado

**Solução:**
1. Configure o `.env` com suas credenciais Gmail:
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=seu-email@gmail.com
SMTP_PASSWORD=sua-app-password-16caracteres
```

2. Gere uma App Password no Gmail:
   - Acesse: https://myaccount.google.com/security
   - Ative "Verificação em 2 passos"
   - Vá em "Senhas de app"
   - Gere senha para "Email"

---

### Erro: "Token inválido ou expirado"
**Causa:** Token não existe ou já foi usado

**Solução:**
```sql
-- Ver tokens ativos
SELECT email, token_verificacao, token_reset_password, token_reset_expiry 
FROM utilizadores 
WHERE email = 'seu-email@example.com';

-- Gerar novo token manualmente (se necessário)
UPDATE utilizadores 
SET token_verificacao = MD5(RAND()), 
    token_reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR)
WHERE email = 'seu-email@example.com';
```

---

## 📝 Checklist de Testes Completo

### Autenticação
- [ ] Teste de conexão (GET test-connection.php) ✅
- [ ] Registar dono (POST register.php) ✅
- [ ] Registar educador (POST register.php) ✅
- [ ] Login (POST login.php) ✅
- [ ] Verificar email (GET verify-email.php) ✅
- [ ] Forgot password (POST forgot-password.php) ✅
- [ ] Reset password (POST reset-password.php) ✅

### Educadores
- [ ] Listar todos (GET api/educadores.php) ✅
- [ ] Buscar por ID (GET api/educadores.php?id=) ✅
- [ ] Buscar por distrito (GET api/educadores.php?distrito=) ✅
- [ ] Buscar por especialidade (GET api/educadores.php?especialidade=) ✅
- [ ] Criar educador (POST api/educadores.php) ✅
- [ ] Atualizar educador (PUT api/educadores.php) ✅
- [ ] Deletar educador (DELETE api/educadores.php) ✅

### Serviços
- [ ] Listar todos (GET api/servicos.php) ✅
- [ ] Buscar por ID (GET api/servicos.php?id=) ✅
- [ ] Criar serviço (POST api/servicos.php) ✅
- [ ] Atualizar serviço (PUT api/servicos.php) ✅
- [ ] Deletar serviço (DELETE api/servicos.php) ✅

### Utilizadores
- [ ] Listar todos (GET api/users.php) ✅
- [ ] Buscar por ID (GET api/users.php?id=) ✅
- [ ] Buscar por tipo (GET api/users.php?tipo=) ✅

---

## 🚀 Executar Todos os Testes de Uma Vez

### No Postman:
1. Clique com botão direito na coleção "AUnidos"
2. Selecione **"Run collection"**
3. Configure:
   - Iterations: 1
   - Delay: 100ms (entre requests)
4. Clique em **"Run AUnidos"**

### Via PowerShell (Script Automatizado):
```powershell
# Na raiz do projeto
.\postman\testar_api.ps1
```

---

## 📚 Recursos Adicionais

- [Documentação Oficial do Postman](https://learning.postman.com/)
- [HTTP Status Codes Reference](https://httpstatuses.com/)
- [JSON Validator](https://jsonlint.com/)
- [Postman Learning Center](https://learning.postman.com/docs/getting-started/introduction/)
- [REST API Best Practices](https://restfulapi.net/)

---

## 📞 Suporte

Se encontrar problemas:
1. Verifique a seção **Troubleshooting** acima
2. Consulte o arquivo `TESTE_RAPIDO.md` para testes manuais
3. Execute o script `postman/testar_api.ps1` para diagnóstico automático
4. Verifique os logs de erro:
   ```powershell
   Get-Content C:\xampp\php\logs\php_error_log -Tail 50
   Get-Content C:\xampp\apache\logs\error.log -Tail 50
   ```

---

**Última atualização:** 26 Nov 2025  
**Versão:** 1.0  
**Projeto:** AUnidos - Plataforma de Educação Canina
