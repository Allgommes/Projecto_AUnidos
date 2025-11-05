# Script de Teste Automático - APIs AUnidos
# Execute: .\test-apis.ps1

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║        TESTE AUTOMÁTICO - APIs AUnidos                     ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan

$baseUrl = "http://localhost/Projecto_AUnidos/api"
$successCount = 0
$failCount = 0

function Test-Endpoint {
    param(
        [string]$Name,
        [string]$Url,
        [string]$Method = "GET",
        [object]$Body = $null
    )
    
    Write-Host "`n┌─────────────────────────────────────────────────────────" -ForegroundColor Yellow
    Write-Host "│ Testando: $Name" -ForegroundColor Yellow
    Write-Host "└─────────────────────────────────────────────────────────" -ForegroundColor Yellow
    
    try {
        if ($Body) {
            $jsonBody = $Body | ConvertTo-Json -Depth 5
            Write-Host "  Método: $Method" -ForegroundColor Gray
            Write-Host "  URL: $Url" -ForegroundColor Gray
            Write-Host "  Body: $jsonBody" -ForegroundColor Gray
            
            $response = Invoke-RestMethod -Uri $Url -Method $Method -Body $jsonBody -ContentType "application/json" -ErrorAction Stop
        } else {
            Write-Host "  Método: $Method" -ForegroundColor Gray
            Write-Host "  URL: $Url" -ForegroundColor Gray
            
            $response = Invoke-RestMethod -Uri $Url -Method $Method -ErrorAction Stop
        }
        
        Write-Host "`n  ✅ SUCESSO!" -ForegroundColor Green
        Write-Host "  Resposta:" -ForegroundColor Cyan
        $response | ConvertTo-Json -Depth 5 | Write-Host -ForegroundColor White
        
        $script:successCount++
        return $response
    }
    catch {
        Write-Host "`n  ❌ ERRO!" -ForegroundColor Red
        Write-Host "  Mensagem: $($_.Exception.Message)" -ForegroundColor Red
        $script:failCount++
        return $null
    }
}

# ====================
# 1. TESTAR CONEXÃO
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  1️⃣  TESTE DE CONEXÃO" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

$connection = Test-Endpoint -Name "Conexão com Banco de Dados" -Url "$baseUrl/test-connection.php"

if (-not $connection) {
    Write-Host "`n❌ ERRO CRÍTICO: Não foi possível conectar ao banco de dados!" -ForegroundColor Red
    Write-Host "Verifique se o MySQL está rodando e se o banco 'aunidos' existe." -ForegroundColor Yellow
    exit 1
}

# ====================
# 2. CRIAR UTILIZADORES
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  2️⃣  CRIAR UTILIZADORES" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

# Criar dono
$randomEmail1 = "joao.$(Get-Random -Minimum 1000 -Maximum 9999)@example.com"
$donoData = @{
    nome = "João Silva"
    email = $randomEmail1
    password = "senha123"
    tipo_utilizador = "dono"
    telefone = "912345678"
    distrito = "Lisboa"
}

$dono = Test-Endpoint -Name "Criar Utilizador Dono" -Url "$baseUrl/users.php" -Method "POST" -Body $donoData
$donoId = $dono.data.id

# Criar educador
$randomEmail2 = "maria.$(Get-Random -Minimum 1000 -Maximum 9999)@example.com"
$educadorData = @{
    nome = "Maria Santos"
    email = $randomEmail2
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
}

$educadorUser = Test-Endpoint -Name "Criar Utilizador Educador" -Url "$baseUrl/users.php" -Method "POST" -Body $educadorData
$educadorUserId = $educadorUser.data.id

# Criar mais um educador
$randomEmail3 = "pedro.$(Get-Random -Minimum 1000 -Maximum 9999)@example.com"
$educador2Data = @{
    nome = "Pedro Costa"
    email = $randomEmail3
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "915555555"
    distrito = "Coimbra"
}

$educador2User = Test-Endpoint -Name "Criar Segundo Educador" -Url "$baseUrl/users.php" -Method "POST" -Body $educador2Data
$educador2UserId = $educador2User.data.id

# ====================
# 3. LISTAR UTILIZADORES
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  3️⃣  LISTAR UTILIZADORES" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

Test-Endpoint -Name "Listar Todos os Utilizadores" -Url "$baseUrl/users.php"

# ====================
# 4. VER UTILIZADOR ESPECÍFICO
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  4️⃣  VER UTILIZADOR ESPECÍFICO" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

if ($donoId) {
    Test-Endpoint -Name "Ver Utilizador Dono (ID: $donoId)" -Url "$baseUrl/users.php?id=$donoId"
}

# ====================
# 5. ATUALIZAR UTILIZADOR
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  5️⃣  ATUALIZAR UTILIZADOR" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

if ($donoId) {
    $updateData = @{
        nome = "João Silva Atualizado"
        telefone = "919999999"
        distrito = "Setúbal"
    }
    Test-Endpoint -Name "Atualizar Utilizador (ID: $donoId)" -Url "$baseUrl/users.php?id=$donoId" -Method "PUT" -Body $updateData
}

# ====================
# 6. CRIAR PERFIS DE EDUCADOR
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  6️⃣  CRIAR PERFIS DE EDUCADOR" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

if ($educadorUserId) {
    $perfilEducador1 = @{
        utilizador_id = $educadorUserId
        biografia = "Educadora canina com 8 anos de experiência em treino de obediência e agility"
        anos_experiencia = 8
        certificacoes = "Certificado Internacional de Educação Canina, Especialização em Agility"
        aprovado = $true
    }
    $educador1 = Test-Endpoint -Name "Criar Perfil Educador 1" -Url "$baseUrl/educadores.php" -Method "POST" -Body $perfilEducador1
    $educadorId1 = $educador1.data.id
}

if ($educador2UserId) {
    $perfilEducador2 = @{
        utilizador_id = $educador2UserId
        biografia = "Especialista em modificação de comportamento com foco em cães resgatados"
        anos_experiencia = 5
        certificacoes = "Certificado em Comportamento Animal"
        aprovado = $true
    }
    $educador2 = Test-Endpoint -Name "Criar Perfil Educador 2" -Url "$baseUrl/educadores.php" -Method "POST" -Body $perfilEducador2
    $educadorId2 = $educador2.data.id
}

# ====================
# 7. LISTAR EDUCADORES
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  7️⃣  LISTAR EDUCADORES" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

Test-Endpoint -Name "Listar Todos os Educadores" -Url "$baseUrl/educadores.php"
Test-Endpoint -Name "Listar Educadores Aprovados" -Url "$baseUrl/educadores.php?aprovado=1"

# ====================
# 8. CRIAR SERVIÇOS
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  8️⃣  CRIAR SERVIÇOS" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

if ($educadorId1) {
    $servico1 = @{
        educador_id = $educadorId1
        nome = "Treino de Obediência Básica"
        descricao = "Treino completo de comandos básicos: sentar, deitar, ficar, vir quando chamado"
        tipo_servico = "individual"
        preco = 50.00
        duracao_estimada = "1 hora"
    }
    Test-Endpoint -Name "Criar Serviço 1 - Obediência" -Url "$baseUrl/servicos.php" -Method "POST" -Body $servico1
    
    $servico2 = @{
        educador_id = $educadorId1
        nome = "Agility - Nível Iniciante"
        descricao = "Introdução ao agility com obstáculos básicos"
        tipo_servico = "grupo"
        preco = 35.00
        duracao_estimada = "45 minutos"
    }
    Test-Endpoint -Name "Criar Serviço 2 - Agility" -Url "$baseUrl/servicos.php" -Method "POST" -Body $servico2
}

if ($educadorId2) {
    $servico3 = @{
        educador_id = $educadorId2
        nome = "Modificação de Comportamento"
        descricao = "Tratamento de problemas comportamentais como ansiedade e agressividade"
        tipo_servico = "individual"
        preco = 80.00
        duracao_estimada = "1.5 horas"
    }
    Test-Endpoint -Name "Criar Serviço 3 - Comportamento" -Url "$baseUrl/servicos.php" -Method "POST" -Body $servico3
}

# ====================
# 9. LISTAR SERVIÇOS
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  9️⃣  LISTAR SERVIÇOS" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

Test-Endpoint -Name "Listar Todos os Serviços" -Url "$baseUrl/servicos.php"

if ($educadorId1) {
    Test-Endpoint -Name "Listar Serviços do Educador 1" -Url "$baseUrl/servicos.php?educador_id=$educadorId1"
}

Test-Endpoint -Name "Listar Serviços Individuais" -Url "$baseUrl/servicos.php?tipo_servico=individual"

# ====================
# 10. ESTATÍSTICAS FINAIS
# ====================
Write-Host "`n`n═══════════════════════════════════════════════════════════" -ForegroundColor Magenta
Write-Host "  🔟  ESTATÍSTICAS FINAIS" -ForegroundColor Magenta
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Magenta

Test-Endpoint -Name "Estatísticas do Banco de Dados" -Url "$baseUrl/test-connection.php"

# ====================
# RESUMO DOS TESTES
# ====================
Write-Host "`n`n╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                  RESUMO DOS TESTES                         ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan

Write-Host "`n  ✅ Testes com sucesso: $successCount" -ForegroundColor Green
Write-Host "  ❌ Testes com falha:   $failCount" -ForegroundColor $(if($failCount -eq 0){"Green"}else{"Red"})
Write-Host "  📊 Total de testes:    $($successCount + $failCount)" -ForegroundColor Cyan

if ($failCount -eq 0) {
    Write-Host "`n  🎉 TODOS OS TESTES PASSARAM COM SUCESSO! 🎉" -ForegroundColor Green
} else {
    Write-Host "`n  ⚠️  Alguns testes falharam. Verifique os erros acima." -ForegroundColor Yellow
}

Write-Host "`n════════════════════════════════════════════════════════════`n" -ForegroundColor Cyan
