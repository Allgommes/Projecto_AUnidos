# 🐕 AUnidos - Plataforma de Educação Canina# 🐕 AUnidos - Marketplace de Educadores Caninos



Plataforma simples que conecta donos de cães a educadores caninos em Portugal.> Plataforma web que conecta donos de cães com educadores caninos profissionais em Portugal.



## 📋 Funcionalidades Principais## 🚀 Funcionalidades



✅ **Registo de Utilizadores** - Donos e Educadores  - ✅ **Sistema de Autenticação** completo (registo, login, verificação de email)

✅ **Verificação de Email** - Confirmação por email  - ✅ **Perfis de Utilizadores** (donos e educadores) com upload de fotos

✅ **Login Seguro** - Autenticação com sessão  - ✅ **Sistema de Busca Avançado** com filtros por localização, especialidade, preço e avaliações

✅ **Recuperação de Password** - Reset via email  - ✅ **Gestão de Serviços** para educadores criarem e gerirem ofertas

✅ **Dashboard** - Painel de controlo básico  - ✅ **Interface Responsiva** moderna com Bootstrap 5.3

✅ **Busca de Educadores** - Pesquisa por distrito  - ✅ **Sistema de Avaliações** e comentários

✅ **Perfil de Utilizador** - Gestão de dados pessoais  - ✅ **Dashboard Personalizado** por tipo de utilizador

- ✅ **Notificações por Email** com PHPMailer

## 🚀 Instalação Rápida

## 📋 Requisitos

### 1. Requisitos

- XAMPP (Apache + MySQL + PHP 7.4+)- **PHP** 8.0 ou superior

- Composer- **MySQL** 5.7 ou superior

- Conta Gmail (para envio de emails)- **Servidor Web** (Apache/Nginx)

- **Extensões PHP**: PDO, MySQLi, mail, mbstring, gd

### 2. Setup do Projeto

### Para XAMPP:

```powershell- XAMPP 8.0 ou superior (já inclui tudo necessário)

# Clone ou baixe o projeto para c:\xampp\htdocs\

cd C:\xampp\htdocs\Projecto_AUnidos## 📥 Instalação



# Instalar dependências### 1. Clone o Repositório

composer install```bash

git clone https://github.com/Allgommes/Projecto_AUnidos.git

# Configurar .envcd Projecto_AUnidos

copy .env.example .env```

# Edite o .env com suas configurações

### 2. Configure a Base de Dados

# Criar banco de dados```bash

& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"# Copie o ficheiro de configuração

& "C:\xampp\mysql\bin\mysql.exe" -u root aunidos -e "source sql/schema.sql"cp config/database.example.php config/database.php

```

# Edite as credenciais da base de dados

### 3. Configurar Email (.env)nano config/database.php

```

```env

SMTP_HOST=smtp.gmail.com### 3. Crie a Base de Dados

SMTP_PORT=587```sql

SMTP_USERNAME=seu-email@gmail.com-- No MySQL/phpMyAdmin:

SMTP_PASSWORD=sua-app-passwordCREATE DATABASE aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

MAIL_FROM_ADDRESS=noreply@aunidos.pt```

MAIL_FROM_NAME=AUnidos

```### 4. Execute o Schema SQL

```bash

**Como obter App Password do Gmail:**# Importe a estrutura das tabelas

1. https://myaccount.google.com/securitymysql -u root -p aunidos < sql/schema.sql

2. Ativar "Verificação em 2 passos"

3. Criar "Senha de app" para Email# Ou use phpMyAdmin para importar sql/schema.sql

4. Colar no `.env````



### 4. Acessar### 5. Configure Permissões (Linux/Mac)

```bash

```chmod 755 uploads/

http://localhost/Projecto_AUnidoschmod 755 uploads/perfis/

``````



## 📁 Estrutura do Projeto### 6. Dados de Teste (Opcional)

```bash

```php inserir-dados-teste.php

Projecto_AUnidos/```

├── index.php              # Página inicial

├── register.php           # Registo de utilizadores## ⚙️ Configuração

├── login.php              # Autenticação

├── verify-email.php       # Verificação de email### Configuração da Base de Dados

├── forgot-password.php    # Solicitar reset de passwordEdite `config/database.php`:

├── reset-password.php     # Redefinir password

├── dashboard.php          # Dashboard do utilizador```php

├── logout.php             # Terminar sessãodefine('DB_HOST', 'localhost');

├── perfil.php             # Editar perfildefine('DB_NAME', 'aunidos');

├── buscar-educadores.php  # Buscar educadoresdefine('DB_USER', 'root');

├── educador.php           # Perfil público do educadordefine('DB_PASS', '');

├── meus-servicos.php      # Gestão de serviços (educador)```

├── bootstrap.php          # Bootstrap da aplicação

│### Configuração de Email

├── app/Para emails funcionarem, configure no `config/database.php`:

│   ├── Helpers/

│   │   └── functions.php  # Funções auxiliares```php

│   ├── Models/define('SMTP_HOST', 'smtp.gmail.com');

│   │   └── User.php       # Model de utilizadordefine('SMTP_USERNAME', 'seu-email@gmail.com');

│   └── Services/define('SMTP_PASSWORD', 'sua-app-password');

│       └── EmailService.php # Envio de emails```

│

├── config/**Para Gmail:**

│   └── database.php       # Configuração DB1. Ative autenticação de 2 fatores

│2. Gere uma "App Password" 

├── resources/views/3. Use essa password no SMTP_PASSWORD

│   ├── auth/              # Views de autenticação

│   └── layouts/           # Layouts (main.php)## 🧪 Teste

│

├── sql/### Dados de Teste Incluídos:

│   └── schema.sql         # Estrutura da BD- **Email**: `email@aunidos.pt`

│- **Password**: `123456`

└── vendor/                # Dependências do Composer- **3 Educadores** criados com perfis completos

```

### Página de Testes:

## 🧪 TestarAcesse `http://localhost/Projecto_AUnidos/teste-navegacao.php`



### Criar Conta## 📁 Estrutura do Projeto

1. Acesse: http://localhost/Projecto_AUnidos/register.php

2. Preencha os dados```

3. Verifique o email recebidoProjecto_AUnidos/

4. Clique no link de verificação├── config/

│   ├── database.php              # Configurações da BD

### Fazer Login│   └── database.example.php      # Exemplo de configuração

1. Acesse: http://localhost/Projecto_AUnidos/login.php├── includes/

2. Use as credenciais criadas│   ├── header.php               # Cabeçalho comum

│   └── footer.php               # Rodapé comum

### Recuperar Password├── src/

1. Acesse: http://localhost/Projecto_AUnidos/forgot-password.php│   └── classes/

2. Insira o email│       ├── User.php             # Gestão de utilizadores

3. Verifique o email recebido│       └── EmailService.php     # Serviços de email

4. Clique no link e defina nova password├── public/

│   ├── css/

## 🛠️ Tecnologias│   └── js/

├── uploads/                     # Uploads de utilizadores

- **Backend:** PHP 7.4+├── sql/

- **Frontend:** HTML5, Bootstrap 5, Bootstrap Icons│   └── schema.sql              # Estrutura da BD

- **Database:** MySQL├── PHPMailer/                  # Biblioteca de email

- **Email:** PHPMailer├── index.php                   # Página inicial

- **Dependências:** Composer (vlucas/phpdotenv)├── login.php                   # Sistema de login

├── register.php                # Registo de utilizadores

## 📝 Licença├── dashboard.php               # Dashboard do utilizador

├── buscar-educadores.php       # Busca de educadores

MIT License - Projeto académico├── educador.php                # Perfil público do educador

├── perfil.php                  # Edição de perfil

---├── meus-servicos.php           # Gestão de serviços

└── README.md                   # Este ficheiro

**Desenvolvido para a escola** 🎓```


## 🎯 Como Usar

### Como Dono de Cão:
1. **Registe-se** como "Dono"
2. **Verifique** o seu email
3. **Faça login** e complete o perfil
4. **Busque educadores** por localização/especialidade
5. **Contacte** educadores através dos perfis

### Como Educador:
1. **Registe-se** como "Educador"
2. **Complete** o perfil profissional
3. **Crie serviços** na página "Meus Serviços"
4. **Gerencie** agendamentos no dashboard

## 🛡️ Segurança

- ✅ Proteção contra **SQL Injection** (PDO preparado)
- ✅ **Hashing seguro** de passwords (password_hash)
- ✅ **Sanitização** de inputs
- ✅ **Validação** server-side e client-side
- ✅ **Sessões seguras** com timeout
- ✅ **Upload seguro** de ficheiros

## 🎨 Tecnologias Utilizadas

- **Backend**: PHP 8+, MySQL
- **Frontend**: Bootstrap 5.3, JavaScript
- **Email**: PHPMailer
- **Ícones**: Bootstrap Icons
- **Autenticação**: PHP Sessions
- **Uploads**: PHP File Upload

## 📊 Base de Dados

### Tabelas Principais:
- `utilizadores` - Dados básicos dos utilizadores
- `educadores` - Perfis dos educadores
- `donos` - Perfis dos donos
- `servicos` - Serviços oferecidos
- `agendamentos` - Reservas de serviços
- `avaliacoes` - Sistema de avaliações
- `especialidades` - Tipos de treino

## 🚀 Deploy em Produção

### 1. Servidor Web:
- Configure virtual host
- SSL/HTTPS obrigatório
- PHP 8.0+ com extensões necessárias

### 2. Base de Dados:
- MySQL 5.7+ ou MariaDB
- Backup automático configurado
- Utilizador com permissões limitadas

### 3. Configurações:
```php
define('DEBUG_MODE', false);        // Desativar debug
define('CACHE_ENABLED', true);      // Ativar cache
define('SESSION_LIFETIME', 7200);   // Sessões mais longas
```

### 4. Segurança:
- Passwords fortes na BD
- Firewall configurado
- Atualizações regulares
- Monitorização de logs

## 🐛 Resolução de Problemas

### Erro de Conexão à BD:
```bash
# Verifique as credenciais em config/database.php
# Teste a conexão MySQL
mysql -u root -p
```

### Emails não funcionam:
- Verifique configurações SMTP
- Confirme App Password do Gmail
- Teste com outros servidores SMTP

### Uploads não funcionam:
```bash
# Linux/Mac - configure permissões
chmod 755 uploads/
chown www-data:www-data uploads/
```

### Página em branco:
- Ative `DEBUG_MODE = true`
- Verifique logs de erro do servidor
- Confirme extensões PHP instaladas

## 📞 Suporte

- **Issues**: [GitHub Issues](https://github.com/Allgommes/Projecto_AUnidos/issues)
- **Email**: gomesalvarogomes@gmail.com
- **Documentação**: Consulte os comentários no código

## 📄 Licença

Este projeto está licenciado sob a MIT License - veja o ficheiro [LICENSE](LICENSE) para detalhes.

## 👨‍💻 Desenvolvedor

**Allgommes** - Desenvolvimento Full Stack

---

**AUnidos** - Conectando donos e educadores caninos em Portugal! 🇵🇹🐕
