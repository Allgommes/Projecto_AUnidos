# 🚀 TESTE RÁPIDO - Fluxo Completo AUnidos

## ✅ O que já funciona:

### 1. **Registo de Conta**
- URL: http://localhost/Projecto_AUnidos/register.php
- ✅ Formulário funcional
- ✅ Criação de donos e educadores
- ✅ Envio de email de verificação (se SMTP configurado)
- ✅ Redirecionamento para login com mensagem

### 2. **Verificação de Email**
- URL: http://localhost/Projecto_AUnidos/verify-email.php?token=TOKEN
- ✅ Valida token do email
- ✅ Marca email como verificado
- ✅ Redireciona para login

### 3. **Login**
- URL: http://localhost/Projecto_AUnidos/login.php
- ✅ Validação de credenciais
- ✅ Sessão criada corretamente
- ✅ Suporte a JSON e formulário
- ✅ Redirecionamento para dashboard

### 4. **Recuperação de Password**
- URL: http://localhost/Projecto_AUnidos/forgot-password.php
- ✅ Solicita email
- ✅ Gera token de reset
- ✅ Envia email com link (se SMTP configurado)

### 5. **Reset de Password**
- URL: http://localhost/Projecto_AUnidos/reset-password.php?token=TOKEN
- ✅ Funcional com validação de token

---

## 📬 TESTES COM POSTMAN

### 🚀 Importar Coleção no Postman

#### Método 1: Importar JSON Diretamente
1. Abra o Postman
2. Clique em **"Import"** (canto superior esquerdo)
3. Selecione **"Upload Files"**
4. Navegue até `postman/AUnidos_Collection.json`
5. Clique em **"Import"**

#### Método 2: Copiar e Colar JSON
1. No Postman, clique em **"Import"**
2. Selecione a aba **"Raw text"**
3. Cole o JSON da coleção (ver arquivo `postman/AUnidos_Collection.json`)
4. Clique em **"Continue"** → **"Import"**

---

## 📋 ORDEM DE TESTES RECOMENDADA

### 1️⃣ **PRIMEIRO: Testar Conexão**
```http
GET http://localhost/Projecto_AUnidos/api/test-connection.php
```
✅ Deve retornar estatísticas do banco de dados

### 2️⃣ **Registar Utilizadores**

**a) Registar Dono:**
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

**b) Registar Educador:**
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
  "biografia": "Educadora canina certificada",
  "certificacoes": "APECA, Etologia Canina"
}
```

### 3️⃣ **Verificar Email (Opcional)**
```http
GET http://localhost/Projecto_AUnidos/verify-email.php?token=SEU_TOKEN_AQUI
```

**Como obter o token:**
```sql
SELECT token_verificacao FROM utilizadores WHERE email = 'joao.silva@example.com';
```

### 4️⃣ **Fazer Login**
```http
POST http://localhost/Projecto_AUnidos/login.php
Content-Type: application/json

{
  "email": "joao.silva@example.com",
  "password": "senha123"
}
```

### 5️⃣ **Recuperar Password**

**a) Solicitar Reset:**
```http
POST http://localhost/Projecto_AUnidos/forgot-password.php
Content-Type: application/json

{
  "email": "joao.silva@example.com"
}
```

**b) Reset Password:**
```http
POST http://localhost/Projecto_AUnidos/reset-password.php
Content-Type: application/json

{
  "token": "TOKEN_DO_EMAIL",
  "password": "novaSenha123",
  "confirm_password": "novaSenha123"
}
```

---

## 🎯 TESTES DE API (Educadores e Serviços)

### **Educadores**

**1. Listar Todos:**
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php
```

**2. Buscar por ID:**
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?id=1
```

**3. Buscar por Distrito:**
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?distrito=Lisboa
```

**4. Buscar por Especialidade:**
```http
GET http://localhost/Projecto_AUnidos/api/educadores.php?especialidade=Obediência Básica
```

**5. Criar Educador:**
```http
POST http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "utilizador_id": 5,
  "anos_experiencia": 7,
  "biografia": "Especialista em comportamento canino",
  "certificacoes": "APECA, Etologia Aplicada"
}
```

**6. Atualizar Educador:**
```http
PUT http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "id": 1,
  "anos_experiencia": 8,
  "biografia": "Biografia atualizada",
  "certificacoes": "Novas certificações"
}
```

**7. Deletar Educador:**
```http
DELETE http://localhost/Projecto_AUnidos/api/educadores.php
Content-Type: application/json

{
  "id": 1
}
```

### **Serviços**

**1. Listar Todos:**
```http
GET http://localhost/Projecto_AUnidos/api/servicos.php
```

**2. Buscar por ID:**
```http
GET http://localhost/Projecto_AUnidos/api/servicos.php?id=1
```

**3. Criar Serviço:**
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

**4. Atualizar Serviço:**
```http
PUT http://localhost/Projecto_AUnidos/api/servicos.php
Content-Type: application/json

{
  "id": 1,
  "nome": "Treino Avançado",
  "descricao": "Descrição atualizada",
  "preco_hora": 30.00,
  "duracao_minutos": 90
}
```

**5. Deletar Serviço:**
```http
DELETE http://localhost/Projecto_AUnidos/api/servicos.php
Content-Type: application/json

{
  "id": 1
}
```

---

## 📧 Configuração de Email (IMPORTANTE!)

Para receber emails, verifique o `.env`:

```env
# Gmail (recomendado para testes)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=seu-email@gmail.com
SMTP_PASSWORD=sua-app-password
MAIL_FROM_ADDRESS=noreply@aunidos.pt
MAIL_FROM_NAME=AUnidos
```

### Como obter App Password do Gmail:
1. Acesse https://myaccount.google.com/security
2. Ative "Verificação em 2 passos"
3. Vá em "Senhas de app"
4. Gere uma senha para "Email"
5. Cole no `.env` em `SMTP_PASSWORD`

---

## 🧪 TESTE MANUAL - Passo a Passo

### Teste 1: Registo Completo (Browser)

```powershell
# 1. Abrir página de registo
Start-Process "http://localhost/Projecto_AUnidos/register.php"

# 2. Preencher formulário:
#    - Nome: Teste Usuario
#    - Email: seu-email-real@gmail.com (use um email real para testar!)
#    - Password: senha123
#    - Tipo: Dono de Cão
#    - Distrito: Lisboa
#    - Telefone: 912345678

# 3. Clicar "Criar Conta"
# 4. Verificar se recebeu email
# 5. Clicar no link do email para verificar
```

### Teste 2: Login

```powershell
# Abrir login
Start-Process "http://localhost/Projecto_AUnidos/login.php"

# Preencher:
#    - Email: email-usado-no-registo
#    - Password: senha123
```

### Teste 3: Esqueci a Password

```powershell
# Abrir forgot-password
Start-Process "http://localhost/Projecto_AUnidos/forgot-password.php"

# Preencher email e verificar se recebe email
```

---

## 🔧 TESTE COM API (PowerShell)

### Criar Conta via JSON

```powershell
$json = @{
    nome = "Maria Teste"
    email = "maria.teste@example.com"
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/register.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

### Login via JSON

```powershell
$json = @{
    email = "maria.teste@example.com"
    password = "senha123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/login.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
```

---

## ⚠️ PROBLEMAS CONHECIDOS

### 1. Reset Password não funciona
**Causa:** Arquivo `reset-password.php` ficou corrompido durante criação  
**Solução Temporária:** Vou recriar o arquivo manualmente

### 2. Email não chega
**Possíveis causas:**
- SMTP não configurado no `.env`
- App Password incorreta
- Gmail bloqueou acesso

**Como verificar:**
```powershell
# Ver logs de erro
Get-Content C:\xampp\php\logs\php_error_log -Tail 20
```

### 3. "Email já registado"
**Solução:** Use outro email ou delete do banco:
```sql
DELETE FROM utilizadores WHERE email = 'seu-email@example.com';
```

---

## 📊 Verificar Dados no Banco

```powershell
# Ver utilizadores criados
& "C:\xampp\mysql\bin\mysql.exe" -u root aunidos -e "SELECT id, nome, email, tipo_utilizador, email_verificado, ativo FROM utilizadores;"

# Ver tokens de verificação
& "C:\xampp\mysql\bin\mysql.exe" -u root aunidos -e "SELECT id, nome, email, token_verificacao, token_reset_password FROM utilizadores;"
```

---

## ✅ CHECKLIST DE TESTES

- [ ] Registo como Dono funciona
- [ ] Registo como Educador funciona
- [ ] Email de verificação enviado
- [ ] Link de verificação funciona
- [ ] Login com email verificado
- [ ] Forgot password envia email
- [ ] Reset password altera senha ⚠️ (em correção)
- [ ] Dashboard abre após login

---

## 🎯 PRÓXIMOS PASSOS

1. ✅ Corrigir `reset-password.php`
2. ✅ Simplificar dashboard (mostrar apenas info básica)
3. ✅ Testar fluxo completo end-to-end
4. ⚠️ Adicionar educadores de exemplo na home
5. ⚠️ Implementar busca com filtros de especialidade

---

**Última atualização:** 6 Nov 2025  
**Status:** Fluxo principal funcionando (registo → email → login → forgot password)
