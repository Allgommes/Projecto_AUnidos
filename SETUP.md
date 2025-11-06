# 🐕 AUnidos - Guia de Configuração e Instalação

## 📋 Índice
1. [Requisitos](#requisitos)
2. [Instalação](#instalação)
3. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
4. [Configuração do Projeto](#configuração-do-projeto)
5. [Configuração de Email](#configuração-de-email)
6. [Testando a Aplicação](#testando-a-aplicação)
7. [Estrutura do Projeto](#estrutura-do-projeto)
8. [Resolução de Problemas](#resolução-de-problemas)

---

## 🔧 Requisitos

Antes de começar, certifique-se de ter instalado:

- **XAMPP** (ou WAMP/LAMP) com:
  - PHP 7.4 ou superior
  - MySQL 5.7 ou superior
  - Apache
- **Composer** (para gerenciar dependências PHP)
- **VSCode** (ou outro editor de código)
- **Navegador Web** (Chrome, Firefox, etc.)

---

## 📥 Instalação

### Passo 1: Instalar o XAMPP

1. Baixe o XAMPP em: https://www.apachefriends.org/
2. Instale no diretório padrão: `C:\xampp`
3. Inicie o **Apache** e **MySQL** no painel de controle do XAMPP

### Passo 2: Instalar o Composer

1. Baixe o Composer em: https://getcomposer.org/download/
2. Execute o instalador e siga as instruções
3. Verifique a instalação abrindo o PowerShell e digitando:
   ```powershell
   composer --version
   ```

### Passo 3: Clonar/Copiar o Projeto

O projeto já está em `C:\xampp\htdocs\Projecto_AUnidos`

### Passo 4: Instalar Dependências

Abra o PowerShell na pasta do projeto e execute:

```powershell
cd C:\xampp\htdocs\Projecto_AUnidos
composer install
```

Isto irá instalar:
- PHPMailer (para envio de emails)
- vlucas/phpdotenv (para gerenciar variáveis de ambiente)

---

## 🗄️ Configuração do Banco de Dados

### Passo 1: Criar a Base de Dados

1. Acesse o phpMyAdmin: http://localhost/phpmyadmin
2. Clique em "Novo" (New) no menu lateral
3. Nome da base de dados: `aunidos`
4. Collation: `utf8mb4_general_ci`
5. Clique em "Criar" (Create)

### Passo 2: Importar o Schema

1. Selecione a base de dados `aunidos`
2. Clique na aba "Importar" (Import)
3. Clique em "Escolher ficheiro" (Choose file)
4. Selecione o arquivo `sql/schema.sql`
5. Clique em "Executar" (Go)

**Se o arquivo `sql/schema.sql` não existir**, execute os seguintes comandos SQL manualmente no phpMyAdmin:

```sql
-- Criar tabela de utilizadores
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    tipo_utilizador ENUM('dono', 'educador') NOT NULL,
    telefone VARCHAR(20),
    distrito VARCHAR(50) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    token_verificacao VARCHAR(64),
    token_reset_password VARCHAR(64),
    token_reset_expiry DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de educadores
CREATE TABLE educadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT UNIQUE NOT NULL,
    biografia TEXT,
    anos_experiencia INT DEFAULT 0,
    certificacoes TEXT,
    foto_perfil VARCHAR(255),
    aprovado BOOLEAN DEFAULT FALSE,
    avaliacao_media DECIMAL(3,2) DEFAULT 0.00,
    total_avaliacoes INT DEFAULT 0,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Criar tabela de donos
CREATE TABLE donos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Criar tabela de serviços
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    educador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco_hora DECIMAL(10,2) NOT NULL,
    duracao_minutos INT DEFAULT 60,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE
);

-- Criar tabela de agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dono_id INT NOT NULL,
    educador_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    observacoes TEXT,
    estado ENUM('pendente', 'confirmado', 'cancelado', 'concluido') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dono_id) REFERENCES donos(id) ON DELETE CASCADE,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE
);

-- Criar tabela de especialidades
CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL
);

-- Criar tabela de relação educador-especialidades
CREATE TABLE educador_especialidades (
    educador_id INT NOT NULL,
    especialidade_id INT NOT NULL,
    PRIMARY KEY (educador_id, especialidade_id),
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id) ON DELETE CASCADE
);

-- Criar tabela de avaliações
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT UNIQUE NOT NULL,
    avaliacao INT NOT NULL CHECK (avaliacao BETWEEN 1 AND 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
);

-- Criar tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    acao VARCHAR(50) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
);

-- Inserir especialidades padrão
INSERT INTO especialidades (nome) VALUES
('Obediência Básica'),
('Adestramento Avançado'),
('Socialização'),
('Correção de Comportamento'),
('Treino para Competições'),
('Treino de Cães de Guarda'),
('Treino de Cães de Assistência'),
('Treino Anti-Puxar'),
('Passeios Educativos'),
('Consultoria Comportamental');
```

---

## ⚙️ Configuração do Projeto

### Passo 1: Configurar Variáveis de Ambiente

1. Abra o arquivo `.env` na raiz do projeto (já foi criado)
2. Configure as credenciais do banco de dados:

```env
# Configurações da Base de Dados
DB_HOST=localhost
DB_NAME=aunidos
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

# URLs do Site
SITE_URL=http://localhost/Projecto_AUnidos

# Configurações de Email (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=seu-email@gmail.com
SMTP_PASSWORD=sua-app-password
MAIL_FROM_ADDRESS=noreply@aunidos.pt
MAIL_FROM_NAME=AUnidos

# Ambiente
DEBUG_MODE=true
```

### Passo 2: Verificar Permissões

Certifique-se de que a pasta `uploads/` tem permissões de escrita.

No Windows/XAMPP, normalmente não é necessário configurar, mas se tiver problemas:
1. Clique com o botão direito na pasta `uploads`
2. Propriedades > Segurança
3. Adicione permissões de escrita para o usuário atual

---

## 📧 Configuração de Email

Para que o sistema possa enviar emails de verificação e recuperação de password:

### Opção 1: Usar Gmail (Recomendado para testes)

1. Acesse sua conta Gmail
2. Vá para: https://myaccount.google.com/security
3. Ative a "Verificação em duas etapas"
4. Vá para: https://myaccount.google.com/apppasswords
5. Gere uma "App Password" para "Mail"
6. Copie a senha gerada (16 caracteres)
7. No arquivo `.env`, configure:
   ```env
   SMTP_USERNAME=seu-email@gmail.com
   SMTP_PASSWORD=xxxx xxxx xxxx xxxx (a app password gerada)
   ```

### Opção 2: Desabilitar Email Temporariamente

Se não quiser configurar o email agora, você pode:
1. Comentar as linhas de envio de email nos Models
2. Testar o sistema sem verificação de email

---

## 🧪 Testando a Aplicação

### Passo 1: Iniciar o XAMPP

1. Abra o painel de controle do XAMPP
2. Inicie o **Apache** e **MySQL**

### Passo 2: Acessar a Aplicação

Abra o navegador e acesse:

```
http://localhost/Projecto_AUnidos
```

### Passo 3: Testar Funcionalidades

#### 1. **Página Inicial**
   - URL: `http://localhost/Projecto_AUnidos/`
   - Deve mostrar a home com estatísticas

#### 2. **Registro de Utilizador**
   - URL: `http://localhost/Projecto_AUnidos/register.php`
   - Preencha o formulário e crie uma conta
   - Escolha "Dono de Cão" ou "Educador Canino"
   - Se o email estiver configurado, receberá um email de verificação
   
    Observações:
    - Em GET (acesso pelo navegador), o `register.php` renderiza o formulário.
    - Em POST (envio do formulário), valida os campos e cria o utilizador com mensagens de feedback (flash) e redirecionamento.
    - Também aceita `application/json` (API). Exemplo de criação via PowerShell:

      ```powershell
      $json = '{"nome":"Maria Teste","email":"maria.teste@example.com","password":"senha123","tipo_utilizador":"dono","distrito":"Lisboa"}'
      Invoke-RestMethod -Uri "http://localhost/Projecto_AUnidos/register.php" -Method Post -Body $json -ContentType "application/json; charset=utf-8" | ConvertTo-Json
      ```

#### 3. **Login**
   - URL: `http://localhost/Projecto_AUnidos/login.php`
   - Use as credenciais criadas
   - Deve redirecionar para o dashboard

#### 4. **Recuperação de Password**
   - URL: `http://localhost/Projecto_AUnidos/forgot-password.php`
   - Insira o email cadastrado
   - Se o email estiver configurado, receberá um link de recuperação

#### 5. **Dashboard**
   - URL: `http://localhost/Projecto_AUnidos/dashboard.php`
   - Visualize o painel de controle
   - O dashboard é diferente para donos e educadores

---

## 📁 Estrutura do Projeto

```
Projecto_AUnidos/
├── app/                      # Código da aplicação
│   ├── Controllers/          # Controladores (AuthController, EducadorController, etc.)
│   ├── Models/              # Models (User, Educador, Servico, Agendamento)
│   ├── Services/            # Serviços (EmailService)
│   └── Helpers/             # Funções auxiliares
├── config/                  # Arquivos de configuração
│   └── database.php         # Configuração do banco de dados
├── resources/               # Recursos
│   └── views/              # Views/Templates
│       ├── auth/           # Views de autenticação
│       ├── educadores/     # Views de educadores
│       ├── dashboard/      # Views de dashboard
│       └── layouts/        # Layouts base
├── sql/                    # Scripts SQL
│   └── schema.sql          # Schema do banco de dados
├── uploads/                # Arquivos enviados pelos usuários
├── vendor/                 # Dependências do Composer (não editar)
├── .env                    # Variáveis de ambiente (NÃO COMITAR)
├── .env.example            # Exemplo de variáveis de ambiente
├── .gitignore              # Arquivos ignorados pelo Git
├── bootstrap.php           # Inicialização da aplicação
├── composer.json           # Dependências do Composer
├── index.php               # Página inicial
├── login.php               # Página de login
├── register.php            # Página de registro
├── forgot-password.php     # Recuperação de password
├── reset-password.php      # Redefinir password
├── verify-email.php        # Verificação de email
├── dashboard.php           # Dashboard do utilizador
└── logout.php              # Logout
```

---

## 🔧 Resolução de Problemas

### Erro: "config/database.php not found"

**Solução:**
1. Verifique se o arquivo `config/database.php` existe
2. Se não existir, copie de `config/database.example.php`
3. Execute: `copy config\database.example.php config\database.php`

### Erro: "Access denied for user 'root'@'localhost'"

**Solução:**
1. Verifique se o MySQL do XAMPP está rodando
2. Verifique as credenciais no arquivo `.env`
3. A password padrão do XAMPP para o usuário `root` é vazia (sem password)

### Erro: "Class 'Dotenv\Dotenv' not found"

**Solução:**
1. Execute: `composer install` na pasta do projeto
2. Verifique se a pasta `vendor/` foi criada

### Erro: "Table 'aunidos.utilizadores' doesn't exist"

**Solução:**
1. Importe o arquivo `sql/schema.sql` no phpMyAdmin
2. Ou execute os comandos SQL manualmente (veja seção "Configuração do Banco de Dados")

### Emails não estão sendo enviados

**Solução:**
1. Verifique se configurou corretamente o SMTP no `.env`
2. Para Gmail, certifique-se de ter gerado uma "App Password"
3. Verifique os logs de erro em `php_error.log`

### Página em branco

**Solução:**
1. Ative o modo de debug no `.env`: `DEBUG_MODE=true`
2. Verifique os logs de erro do Apache em: `C:\xampp\apache\logs\error.log`
3. Verifique se todas as dependências foram instaladas: `composer install`

---

## 📝 Próximos Passos

Após configurar e testar o sistema, você pode:

1. **Criar usuários de teste** (dono e educador)
2. **Adicionar serviços** (se for educador)
3. **Buscar educadores** (se for dono)
4. **Fazer agendamentos**
5. **Personalizar o design** editando os arquivos em `resources/views/`

---

## 🆘 Suporte

Se encontrar problemas:
1. Verifique esta documentação
2. Verifique os logs de erro
3. Verifique se todos os requisitos estão instalados
4. Certifique-se de que o Apache e MySQL estão rodando

---

## 📌 Notas Importantes

- ⚠️ **Nunca comite o arquivo `.env`** para o Git (ele contém credenciais sensíveis)
- ⚠️ Em **produção**, altere `DEBUG_MODE=false` no `.env`
- ⚠️ Use **passwords fortes** para o banco de dados em produção
- ⚠️ Configure **SSL/HTTPS** em produção

---

## ✅ Checklist de Configuração

- [ ] XAMPP instalado e rodando
- [ ] Composer instalado
- [ ] Dependências instaladas (`composer install`)
- [ ] Base de dados `aunidos` criada
- [ ] Schema SQL importado
- [ ] Arquivo `.env` configurado
- [ ] Email SMTP configurado (opcional)
- [ ] Testado registro de usuário
- [ ] Testado login
- [ ] Testado recuperação de password

---

**Desenvolvido com ❤️ para o projeto AUnidos**
