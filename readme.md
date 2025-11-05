# 🐕 AUnidos - Marketplace de Educadores Caninos

> Plataforma web que conecta donos de cães com educadores caninos profissionais em Portugal.

## 🚀 Funcionalidades

- ✅ **Sistema de Autenticação** completo (registo, login, verificação de email)
- ✅ **Perfis de Utilizadores** (donos e educadores) com upload de fotos
- ✅ **Sistema de Busca Avançado** com filtros por localização, especialidade, preço e avaliações
- ✅ **Gestão de Serviços** para educadores criarem e gerirem ofertas
- ✅ **Interface Responsiva** moderna com Bootstrap 5.3
- ✅ **Sistema de Avaliações** e comentários
- ✅ **Dashboard Personalizado** por tipo de utilizador
- ✅ **Notificações por Email** com PHPMailer

## 📋 Requisitos

- **PHP** 8.0 ou superior
- **MySQL** 5.7 ou superior
- **Servidor Web** (Apache/Nginx)
- **Extensões PHP**: PDO, MySQLi, mail, mbstring, gd

### Para XAMPP:
- XAMPP 8.0 ou superior (já inclui tudo necessário)

## 📥 Instalação

### 1. Clone o Repositório
```bash
git clone https://github.com/Allgommes/Projecto_AUnidos.git
cd Projecto_AUnidos
```

### 2. Configure a Base de Dados
```bash
# Copie o ficheiro de configuração
cp config/database.example.php config/database.php

# Edite as credenciais da base de dados
nano config/database.php
```

### 3. Crie a Base de Dados
```sql
-- No MySQL/phpMyAdmin:
CREATE DATABASE aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Execute o Schema SQL
```bash
# Importe a estrutura das tabelas
mysql -u root -p aunidos < sql/schema.sql

# Ou use phpMyAdmin para importar sql/schema.sql
```

### 5. Configure Permissões (Linux/Mac)
```bash
chmod 755 uploads/
chmod 755 uploads/perfis/
```

### 6. Dados de Teste (Opcional)
```bash
php inserir-dados-teste.php
```

## ⚙️ Configuração

### Configuração da Base de Dados
Edite `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aunidos');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Configuração de Email
Para emails funcionarem, configure no `config/database.php`:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'seu-email@gmail.com');
define('SMTP_PASSWORD', 'sua-app-password');
```

**Para Gmail:**
1. Ative autenticação de 2 fatores
2. Gere uma "App Password" 
3. Use essa password no SMTP_PASSWORD

## 🧪 Teste

### Dados de Teste Incluídos:
- **Email**: `email@aunidos.pt`
- **Password**: `123456`
- **3 Educadores** criados com perfis completos

### Página de Testes:
Acesse `http://localhost/Projecto_AUnidos/teste-navegacao.php`

## 📁 Estrutura do Projeto

```
Projecto_AUnidos/
├── config/
│   ├── database.php              # Configurações da BD
│   └── database.example.php      # Exemplo de configuração
├── includes/
│   ├── header.php               # Cabeçalho comum
│   └── footer.php               # Rodapé comum
├── src/
│   └── classes/
│       ├── User.php             # Gestão de utilizadores
│       └── EmailService.php     # Serviços de email
├── public/
│   ├── css/
│   └── js/
├── uploads/                     # Uploads de utilizadores
├── sql/
│   └── schema.sql              # Estrutura da BD
├── PHPMailer/                  # Biblioteca de email
├── index.php                   # Página inicial
├── login.php                   # Sistema de login
├── register.php                # Registo de utilizadores
├── dashboard.php               # Dashboard do utilizador
├── buscar-educadores.php       # Busca de educadores
├── educador.php                # Perfil público do educador
├── perfil.php                  # Edição de perfil
├── meus-servicos.php           # Gestão de serviços
└── README.md                   # Este ficheiro
```

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
