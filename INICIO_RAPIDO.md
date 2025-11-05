# 🎯 GUIA RÁPIDO DE INÍCIO - AUnidos

## 📖 ANTES DE COMEÇAR, LEIA ISTO!

Você tem **4 documentos principais** para te ajudar:

| Documento | Quando Usar | Tempo |
|-----------|-------------|-------|
| **📘 SETUP.md** | 🏁 **COMECE AQUI** - Guia completo passo a passo | 30-60 min |
| **📙 MIGRATION.md** | Depois do SETUP - Código para atualizar arquivos | 1-2 horas |
| **📗 CHECKLIST.md** | Durante todo o processo - Acompanhar progresso | Contínuo |
| **📕 COMANDOS.md** | Referência rápida - Copiar/colar comandos | Conforme necessário |
| **📓 RESUMO.md** | Visão geral - Entender o que foi feito | 10 min |

---

## ⚡ INÍCIO SUPER RÁPIDO (5 Minutos)

Se você só quer ver o site funcionando AGORA:

```powershell
# 1. Abra o PowerShell na pasta do projeto
cd C:\xampp\htdocs\Projecto_AUnidos

# 2. Instale dependências
composer install

# 3. Copie o arquivo de configuração
copy .env.example .env

# 4. Crie o banco de dados
mysql -u root -e "CREATE DATABASE aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# 5. Importe o schema
mysql -u root aunidos < sql\schema.sql

# 6. Abra no navegador
Start-Process "http://localhost/Projecto_AUnidos/"
```

**✅ FEITO!** O site deve abrir no seu navegador.

**⚠️ MAS ATENÇÃO:** Ainda precisará atualizar os arquivos PHP (veja MIGRATION.md)

---

## 🎓 ROTEIRO COMPLETO (Recomendado)

### Dia 1 - Configuração (1-2 horas)

1. ✅ **Leia `SETUP.md` seções 1-5**
   - Requisitos
   - Instalação
   - Configuração do banco
   - Configuração do projeto
   
2. ✅ **Configure tudo:**
   - Instale XAMPP e Composer
   - Execute `composer install`
   - Crie `.env`
   - Crie e configure o banco de dados
   
3. ✅ **Teste básico:**
   - Acesse http://localhost/Projecto_AUnidos/
   - Veja se a home carrega (pode ter erros ainda, tudo bem!)

### Dia 2 - Migração do Código (2-4 horas)

1. ✅ **Leia `MIGRATION.md` completamente**
   
2. ✅ **Faça backup dos arquivos originais:**
   ```powershell
   New-Item -ItemType Directory -Force -Path backup
   Copy-Item *.php backup\ -Force
   ```
   
3. ✅ **Atualize os arquivos PHP um por um:**
   - login.php
   - register.php
   - forgot-password.php
   - reset-password.php
   - verify-email.php (criar novo)
   - logout.php
   - dashboard.php
   - buscar-educadores.php
   - educador.php
   - perfil.php
   - meus-servicos.php
   
4. ✅ **Crie as views que faltam:**
   - Dashboard (educador e dono)
   - Busca de educadores
   - Perfil de educador

5. ✅ **Limpe arquivos desnecessários**
   ```powershell
   Remove-Item -Recurse -Force PHPMailer, react-native-projects
   ```

### Dia 3 - Testes e Ajustes (1-2 horas)

1. ✅ **Use `CHECKLIST.md`** para testar tudo:
   - Registro de usuário
   - Login
   - Recuperação de password
   - Dashboard
   - Busca de educadores
   - Perfis

2. ✅ **Configure email (opcional):**
   - Veja SETUP.md seção 5
   - Configure Gmail com App Password

3. ✅ **Resolva problemas:**
   - Use SETUP.md seção 8 (Resolução de Problemas)
   - Verifique logs de erro
   - Consulte COMANDOS.md para comandos úteis

---

## 🔥 OS 3 ERROS MAIS COMUNS

### Erro 1: "Failed opening required 'config/database.php'"
**Solução:**
```powershell
copy config\database.example.php config\database.php
```

### Erro 2: "Access denied for user 'root'@'localhost'"
**Solução:**
- Verifique se MySQL do XAMPP está rodando
- No XAMPP, a senha do root é vazia (deixe em branco no `.env`)

### Erro 3: "Class 'Dotenv\Dotenv' not found"
**Solução:**
```powershell
composer install
```

---

## 📊 ESTRUTURA DO PROJETO SIMPLIFICADA

```
Projecto_AUnidos/
│
├── 📱 ARQUIVOS PHP NA RAIZ (Rotas - você precisa atualizar)
│   ├── index.php             ← Página inicial
│   ├── login.php             ← ⚠️ ATUALIZAR
│   ├── register.php          ← ⚠️ ATUALIZAR
│   ├── dashboard.php         ← ⚠️ ATUALIZAR
│   └── ...
│
├── 🎮 app/                   (Lógica da aplicação - JÁ CRIADO)
│   ├── Controllers/          ← AuthController, DashboardController, etc.
│   ├── Models/              ← User, Educador, Servico, Agendamento
│   ├── Services/            ← EmailService
│   └── Helpers/             ← Funções auxiliares
│
├── 🎨 resources/views/       (Templates HTML - ALGUNS CRIADOS)
│   ├── auth/                ← ✅ Login, registro, etc. (CRIADO)
│   ├── dashboard/           ← ⚠️ CRIAR (veja MIGRATION.md)
│   ├── educadores/          ← ⚠️ CRIAR (veja MIGRATION.md)
│   └── layouts/             ← ✅ Layout principal (CRIADO)
│
├── ⚙️ config/                (Configurações - JÁ CONFIGURADO)
│   └── database.php         ← Conexão com banco
│
├── 🗄️ sql/                   (Scripts SQL)
│   └── schema.sql           ← Estrutura do banco
│
├── 📦 vendor/                (Dependências - Composer instala)
│
└── 📄 DOCUMENTAÇÃO          (GUIAS - LEIA!)
    ├── SETUP.md             ← 🏁 **COMECE AQUI**
    ├── MIGRATION.md         ← Depois do setup
    ├── CHECKLIST.md         ← Use durante o processo
    ├── COMANDOS.md          ← Referência rápida
    └── RESUMO.md            ← Visão geral
```

---

## 🎯 CHECKLIST ULTRA-RÁPIDO

Imprima ou salve isto e vá marcando:

```
□ XAMPP instalado e rodando
□ Composer instalado
□ composer install executado
□ .env criado e configurado
□ Banco de dados 'aunidos' criado
□ Schema SQL importado
□ Backup dos arquivos originais feito
□ login.php atualizado
□ register.php atualizado
□ dashboard.php atualizado
□ forgot-password.php atualizado
□ reset-password.php atualizado
□ verify-email.php criado
□ logout.php atualizado
□ Views do dashboard criadas
□ Pastas PHPMailer e react-native-projects removidas
□ Arquivos de teste removidos
□ Registro funciona
□ Login funciona
□ Recuperação de password funciona (se email configurado)
□ Dashboard funciona
```

---

## 💡 DICAS PRO

1. **Use o VSCode** - Tem syntax highlighting e autocomplete
2. **Mantenha o CHECKLIST.md aberto** - Marque conforme avança
3. **Não pule etapas** - Siga a ordem do SETUP.md
4. **Teste após cada mudança** - Não faça tudo de uma vez
5. **Consulte COMANDOS.md** - Tem todos os comandos prontos
6. **Leia os erros** - PHP mostra exatamente o que está errado
7. **Use o Git** - Faça commits frequentes
8. **Email pode esperar** - Configure depois se quiser

---

## 🆘 SE FICAR PERDIDO

1. **Respire** 😌
2. **Volte ao SETUP.md** - Releia a seção relevante
3. **Verifique CHECKLIST.md** - Veja o que falta
4. **Consulte COMANDOS.md** - Comandos prontos pra copiar
5. **Veja os logs** - `C:\xampp\apache\logs\error.log`
6. **Google é seu amigo** - Procure a mensagem de erro exata

---

## 🎬 PRONTO PARA COMEÇAR?

### Passo 1: Abra 3 abas no navegador

1. **Aba 1:** Este guia (INICIO_RAPIDO.md)
2. **Aba 2:** SETUP.md
3. **Aba 3:** CHECKLIST.md

### Passo 2: Abra o PowerShell

```powershell
cd C:\xampp\htdocs\Projecto_AUnidos
```

### Passo 3: Comece!

Siga o **SETUP.md** passo a passo, usando o **CHECKLIST.md** para marcar seu progresso.

---

## 📞 RESUMO EXECUTIVO

| Item | Status | Ação |
|------|--------|------|
| **Estrutura MVC** | ✅ Criada | Pronta para uso |
| **Composer** | ✅ Configurado | Execute `composer install` |
| **Models** | ✅ Criados | User, Educador, Servico, Agendamento |
| **Controllers** | ✅ Criados | Auth, Dashboard, Educador |
| **Services** | ✅ Criado | EmailService |
| **Views (Auth)** | ✅ Criadas | Login, registro, password |
| **Views (Dashboard)** | ⚠️ Criar | Veja MIGRATION.md |
| **Views (Educadores)** | ⚠️ Criar | Veja MIGRATION.md |
| **Config** | ✅ Pronto | Configure .env |
| **Helpers** | ✅ Criados | 20+ funções |
| **Documentação** | ✅ Completa | 5 guias detalhados |

---

## 🚦 SEMÁFORO DE PRIORIDADES

### 🔴 FAÇA AGORA (Crítico)
1. Instalar XAMPP e Composer
2. Executar `composer install`
3. Criar `.env`
4. Criar banco de dados
5. Importar schema SQL

### 🟡 FAÇA DEPOIS (Importante)
1. Atualizar arquivos PHP na raiz
2. Criar views que faltam
3. Testar todas as funcionalidades
4. Limpar arquivos antigos

### 🟢 FAÇA SE TIVER TEMPO (Opcional)
1. Configurar email SMTP
2. Personalizar design
3. Adicionar dados de teste
4. Configurar Git

---

## 🎉 VOCÊ CONSEGUE!

Este projeto está **90% pronto**. Você só precisa:
1. Configurar o ambiente (20 min)
2. Atualizar alguns arquivos (1-2 horas)
3. Testar (30 min)

**Total: 2-3 horas** para ter tudo funcionando perfeitamente!

---

**BOA SORTE! 🍀**

**Lembre-se:** SETUP.md é seu melhor amigo. Comece por lá!

---

📅 Criado em: 5 de Novembro de 2025
👨‍💻 Para: Álvaro Gomes
🎯 Projeto: AUnidos - Conectando Donos e Educadores Caninos
