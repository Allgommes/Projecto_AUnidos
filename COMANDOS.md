# ⚡ Comandos Rápidos - AUnidos

Copie e cole estes comandos no PowerShell para executar rapidamente as tarefas mais comuns.

---

## 📂 Navegação

```powershell
# Ir para a pasta do projeto
cd C:\xampp\htdocs\Projecto_AUnidos
```

---

## 📦 Composer

```powershell
# Instalar dependências
composer install

# Atualizar dependências
composer update

# Ver dependências instaladas
composer show

# Verificar se o autoload está funcionando
composer dump-autoload
```

---

## 🗄️ Banco de Dados

### Via PowerShell/CMD

```powershell
# Criar banco de dados
mysql -u root -e "CREATE DATABASE IF NOT EXISTS aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"

# Importar schema
mysql -u root aunidos < sql\schema.sql

# Fazer backup do banco
mysqldump -u root aunidos > backup_aunidos.sql

# Ver tabelas
mysql -u root aunidos -e "SHOW TABLES;"
```

### Via phpMyAdmin

```
# Acesse: http://localhost/phpmyadmin
```

---

## 🔧 Configuração

```powershell
# Copiar arquivo de exemplo para .env
copy .env.example .env

# Abrir .env no Notepad
notepad .env

# Abrir .env no VSCode
code .env
```

---

## 🗑️ Limpeza de Arquivos

```powershell
# Remover pastas e arquivos desnecessários
Remove-Item -Recurse -Force PHPMailer -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force react-native-projects -ErrorAction SilentlyContinue
Remove-Item teste-navegacao.php -ErrorAction SilentlyContinue
Remove-Item teste-preco.html -ErrorAction SilentlyContinue
Remove-Item inserir-dados-teste.php -ErrorAction SilentlyContinue
Remove-Item aunidos.xml -ErrorAction SilentlyContinue

Write-Host "Limpeza concluída!" -ForegroundColor Green
```

---

## 📋 Backup

```powershell
# Criar pasta de backup
New-Item -ItemType Directory -Force -Path backup

# Fazer backup de arquivos importantes
Copy-Item -Path login.php -Destination backup\login.php.bak -Force
Copy-Item -Path register.php -Destination backup\register.php.bak -Force
Copy-Item -Path dashboard.php -Destination backup\dashboard.php.bak -Force
Copy-Item -Path config\database.php -Destination backup\database.php.bak -Force

Write-Host "Backup criado na pasta 'backup'!" -ForegroundColor Green
```

---

## 🌐 Abrir no Navegador

```powershell
# Abrir página inicial
Start-Process "http://localhost/Projecto_AUnidos/"

# Abrir phpMyAdmin
Start-Process "http://localhost/phpmyadmin"

# Abrir painel do XAMPP
Start-Process "http://localhost/dashboard"
```

---

## 📊 Verificações Rápidas

```powershell
# Verificar se PHP está instalado
php --version

# Verificar se Composer está instalado
composer --version

# Verificar se MySQL está rodando
Get-Process mysqld -ErrorAction SilentlyContinue

# Verificar se Apache está rodando
Get-Process httpd -ErrorAction SilentlyContinue

# Listar todos os processos XAMPP
Get-Process | Where-Object {$_.ProcessName -like "*mysql*" -or $_.ProcessName -like "*httpd*" -or $_.ProcessName -like "*apache*"}
```

---

## 📝 Logs

```powershell
# Ver últimas linhas do log de erro do Apache
Get-Content C:\xampp\apache\logs\error.log -Tail 50

# Limpar log de erro do Apache
Clear-Content C:\xampp\apache\logs\error.log

# Ver log de acesso do Apache
Get-Content C:\xampp\apache\logs\access.log -Tail 20
```

---

## 🔍 Debug

```powershell
# Verificar sintaxe de um arquivo PHP
php -l login.php

# Executar um script PHP
php script.php

# Ver informações do PHP
php -i

# Ver módulos carregados
php -m
```

---

## 🎨 VSCode

```powershell
# Abrir o projeto no VSCode
code .

# Abrir arquivo específico
code login.php
code config\database.php
code .env
```

---

## 🔒 Permissões (se necessário)

```powershell
# Dar permissões de escrita na pasta uploads
icacls uploads /grant Everyone:F /T

# Verificar permissões
icacls uploads
```

---

## 🚀 Iniciar XAMPP (via linha de comando)

```powershell
# Iniciar Apache
& "C:\xampp\apache_start.bat"

# Iniciar MySQL
& "C:\xampp\mysql_start.bat"

# Parar Apache
& "C:\xampp\apache_stop.bat"

# Parar MySQL
& "C:\xampp\mysql_stop.bat"
```

---

## 📦 Comandos Úteis do Git

```powershell
# Inicializar repositório Git
git init

# Ver status
git status

# Adicionar todos os arquivos (exceto os do .gitignore)
git add .

# Commit
git commit -m "Mensagem do commit"

# Ver histórico
git log --oneline

# Criar branch
git branch nome-da-branch

# Mudar de branch
git checkout nome-da-branch

# Ver branches
git branch -a
```

---

## 🧪 Testar Conexão com o Banco

```powershell
# Criar arquivo de teste
@"
<?php
require_once 'config/database.php';
try {
    `$db = getDB();
    echo "✅ Conexão com o banco de dados bem-sucedida!";
    echo "\nTabelas:\n";
    `$tables = `$db->query("SHOW TABLES")->fetchAll();
    foreach (`$tables as `$table) {
        echo "- " . `$table[0] . "\n";
    }
} catch (Exception `$e) {
    echo "❌ Erro: " . `$e->getMessage();
}
"@ | Out-File -FilePath test-db.php -Encoding UTF8

# Executar teste
php test-db.php

# Remover arquivo de teste
Remove-Item test-db.php
```

---

## 📧 Testar Envio de Email

```powershell
# Criar script de teste
@"
<?php
require_once 'bootstrap.php';
use App\Services\EmailService;

`$emailService = new EmailService();
`$result = `$emailService->sendEmail(
    'seu-email@gmail.com',
    'Teste - AUnidos',
    '<h1>Email de Teste</h1><p>Se recebeu isto, o email está funcionando!</p>'
);

echo `$result ? "✅ Email enviado!" : "❌ Erro ao enviar email";
"@ | Out-File -FilePath test-email.php -Encoding UTF8

# Executar teste
php test-email.php

# Remover arquivo de teste
Remove-Item test-email.php
```

---

## 🔄 Resetar Tudo (Cuidado!)

```powershell
# Este comando apaga o banco de dados e recria do zero
# USE COM CUIDADO!

# Confirmar
`$confirm = Read-Host "Tem certeza que deseja resetar TUDO? (digite SIM)"

if (`$confirm -eq "SIM") {
    # Dropar banco
    mysql -u root -e "DROP DATABASE IF EXISTS aunidos;"
    
    # Criar banco
    mysql -u root -e "CREATE DATABASE aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
    
    # Importar schema
    mysql -u root aunidos < sql\schema.sql
    
    Write-Host "✅ Banco de dados resetado!" -ForegroundColor Green
} else {
    Write-Host "❌ Operação cancelada." -ForegroundColor Yellow
}
```

---

## 📊 Estatísticas do Projeto

```powershell
# Contar linhas de código PHP
Get-ChildItem -Path . -Include *.php -Recurse -File | Get-Content | Measure-Object -Line

# Contar arquivos por tipo
@{
    "PHP" = (Get-ChildItem -Path . -Filter *.php -Recurse -File).Count
    "CSS" = (Get-ChildItem -Path . -Filter *.css -Recurse -File).Count
    "JS" = (Get-ChildItem -Path . -Filter *.js -Recurse -File).Count
    "SQL" = (Get-ChildItem -Path . -Filter *.sql -Recurse -File).Count
} | Format-Table -AutoSize

# Tamanho total do projeto
"{0:N2} MB" -f ((Get-ChildItem -Path . -Recurse -File | Measure-Object -Property Length -Sum).Sum / 1MB)
```

---

## 🆘 Comandos de Ajuda

```powershell
# Ver comandos disponíveis do PHP
php --help

# Ver comandos disponíveis do Composer
composer list

# Ver comandos disponíveis do MySQL
mysql --help
```

---

## 📱 Criar Atalho no Desktop

```powershell
# Criar atalho para abrir o projeto no navegador
`$WshShell = New-Object -comObject WScript.Shell
`$Shortcut = `$WshShell.CreateShortcut("`$Home\Desktop\AUnidos.lnk")
`$Shortcut.TargetPath = "http://localhost/Projecto_AUnidos/"
`$Shortcut.Save()

Write-Host "✅ Atalho criado no Desktop!" -ForegroundColor Green
```

---

## 🎯 Setup Rápido (Tudo de Uma Vez)

```powershell
# ATENÇÃO: Este comando executa TODO o setup
# Use apenas se for a primeira vez e quer fazer tudo automaticamente

function Setup-AUnidos {
    Write-Host "🚀 Iniciando setup do AUnidos..." -ForegroundColor Cyan
    
    # 1. Instalar dependências
    Write-Host "`n📦 Instalando dependências..." -ForegroundColor Yellow
    composer install
    
    # 2. Copiar .env
    if (-not (Test-Path .env)) {
        Write-Host "`n⚙️ Criando arquivo .env..." -ForegroundColor Yellow
        Copy-Item .env.example .env
    }
    
    # 3. Criar banco
    Write-Host "`n🗄️ Criando banco de dados..." -ForegroundColor Yellow
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
    
    # 4. Importar schema
    Write-Host "`n📋 Importando schema..." -ForegroundColor Yellow
    mysql -u root aunidos < sql\schema.sql
    
    # 5. Limpar arquivos antigos
    Write-Host "`n🗑️ Limpando arquivos desnecessários..." -ForegroundColor Yellow
    Remove-Item -Recurse -Force PHPMailer,react-native-projects -ErrorAction SilentlyContinue
    Remove-Item teste-*.php,teste-*.html,inserir-dados-teste.php,aunidos.xml -ErrorAction SilentlyContinue
    
    # 6. Abrir no navegador
    Write-Host "`n🌐 Abrindo no navegador..." -ForegroundColor Yellow
    Start-Sleep -Seconds 2
    Start-Process "http://localhost/Projecto_AUnidos/"
    
    Write-Host "`n✅ Setup concluído!" -ForegroundColor Green
    Write-Host "📝 Não esqueça de configurar o arquivo .env com suas credenciais!" -ForegroundColor Cyan
}

# Para executar, descomente a linha abaixo:
# Setup-AUnidos
```

---

**💡 Dica:** Salve este arquivo e mantenha aberto para consulta rápida!

**⚠️ Importante:** Sempre execute comandos críticos (como DROP DATABASE) com cuidado!

---

**Última atualização:** 5 de Novembro de 2025
