# 🚀 Script de Teste Automático da API AUnidos
# Uso: .\api\testar_todas_apis.ps1

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║       🧪 TESTE AUTOMÁTICO DE TODAS AS APIs - AUnidos      ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

$baseUrl = "http://localhost/Projecto_AUnidos"
$headers = @{
    "Content-Type" = "application/json"
}

$totalTestes = 0
$testesPassados = 0
$testesFalhados = 0

function Test-Endpoint {
    param(
        [string]$Nome,
        [string]$Metodo,
        [string]$Url,
        [string]$Body = $null
    )
    
    $script:totalTestes++
    Write-Host "$script:totalTestes. " -NoNewline -ForegroundColor Yellow
    Write-Host "$Nome" -NoNewline -ForegroundColor Cyan
    Write-Host " [$Metodo]" -ForegroundColor Gray
    
    try {
        $params = @{
            Uri = $Url
            Method = $Metodo
            Headers = $headers
        }
        
        if ($Body) {
            $params['Body'] = $Body
        }
        
        $response = Invoke-RestMethod @params
        
        if ($response.success) {
            Write-Host "   ✅ PASSOU" -ForegroundColor Green
            $script:testesPassados++
            return $response
        } else {
            Write-Host "   ⚠️  AVISO: $($response.error)" -ForegroundColor Yellow
            $script:testesPassados++
            return $response
        }
    } catch {
        Write-Host "   ❌ FALHOU: $($_.Exception.Message)" -ForegroundColor Red
        $script:testesFalhados++
        return $null
    }
}

Write-Host "═══ TESTES DE CONEXÃO ═══" -ForegroundColor Magenta
Write-Host ""

# Teste 1: Conexão
$conn = Test-Endpoint -Nome "Testar Conexão com BD" -Metodo "GET" -Url "$baseUrl/api/test-connection.php"
if ($conn) {
    Write-Host "      → Utilizadores: $($conn.data.total_utilizadores)" -ForegroundColor Gray
    Write-Host "      → Educadores: $($conn.data.total_educadores)" -ForegroundColor Gray
    Write-Host "      → Donos: $($conn.data.total_donos)" -ForegroundColor Gray
    Write-Host "      → Serviços: $($conn.data.total_servicos)" -ForegroundColor Gray
}
Write-Host ""

Write-Host "═══ TESTES DE EDUCADORES ═══" -ForegroundColor Magenta
Write-Host ""

# Teste 2: Listar Educadores
$educadores = Test-Endpoint -Nome "Listar Todos Educadores" -Metodo "GET" -Url "$baseUrl/api/educadores.php"
if ($educadores -and $educadores.data.Count -gt 0) {
    Write-Host "      → Total: $($educadores.data.Count) educadores" -ForegroundColor Gray
    $primeiroEducador = $educadores.data[0]
    
    # Teste 3: Buscar Educador por ID
    Test-Endpoint -Nome "Buscar Educador por ID" -Metodo "GET" -Url "$baseUrl/api/educadores.php?id=$($primeiroEducador.id)" | Out-Null
    
    # Teste 4: Buscar por Distrito
    if ($primeiroEducador.distrito) {
        Test-Endpoint -Nome "Buscar por Distrito" -Metodo "GET" -Url "$baseUrl/api/educadores.php?distrito=$($primeiroEducador.distrito)" | Out-Null
    }
}

# Teste 5: Buscar por Especialidade
Test-Endpoint -Nome "Buscar por Especialidade" -Metodo "GET" -Url "$baseUrl/api/educadores.php?especialidade=Obediência Básica" | Out-Null

Write-Host ""
Write-Host "═══ TESTES DE SERVIÇOS ═══" -ForegroundColor Magenta
Write-Host ""

# Teste 6: Listar Serviços
$servicos = Test-Endpoint -Nome "Listar Todos Serviços" -Metodo "GET" -Url "$baseUrl/api/servicos.php"
if ($servicos) {
    Write-Host "      → Total: $($servicos.data.Count) serviços" -ForegroundColor Gray
}

# Teste 7: Criar Serviço
if ($educadores -and $educadores.data.Count -gt 0) {
    $educadorId = $educadores.data[0].id
    $servicoData = @{
        educador_id = $educadorId
        nome = "Teste API Automatizado $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
        descricao = "Serviço criado automaticamente para teste da API"
        preco_hora = 25.50
        duracao_minutos = 60
    } | ConvertTo-Json -Compress
    
    $novoServico = Test-Endpoint -Nome "Criar Novo Serviço" -Metodo "POST" -Url "$baseUrl/api/servicos.php" -Body $servicoData
    
    if ($novoServico -and $novoServico.data.id) {
        $servicoId = $novoServico.data.id
        Write-Host "      → ID do serviço: $servicoId" -ForegroundColor Gray
        
        # Teste 8: Buscar Serviço Criado
        Test-Endpoint -Nome "Buscar Serviço por ID" -Metodo "GET" -Url "$baseUrl/api/servicos.php?id=$servicoId" | Out-Null
        
        # Teste 9: Atualizar Serviço
        $updateData = @{
            id = $servicoId
            preco_hora = 30.00
            descricao = "Descrição atualizada automaticamente"
        } | ConvertTo-Json -Compress
        
        Test-Endpoint -Nome "Atualizar Serviço" -Metodo "PUT" -Url "$baseUrl/api/servicos.php" -Body $updateData | Out-Null
        
        # Teste 10: Deletar Serviço
        $deleteData = @{ id = $servicoId } | ConvertTo-Json -Compress
        Test-Endpoint -Nome "Deletar Serviço" -Metodo "DELETE" -Url "$baseUrl/api/servicos.php" -Body $deleteData | Out-Null
    }
}

Write-Host ""
Write-Host "═══ TESTES DE UTILIZADORES ═══" -ForegroundColor Magenta
Write-Host ""

# Teste 11: Listar Utilizadores
$users = Test-Endpoint -Nome "Listar Todos Utilizadores" -Metodo "GET" -Url "$baseUrl/api/users.php"
if ($users) {
    Write-Host "      → Total: $($users.total) utilizadores" -ForegroundColor Gray
    
    if ($users.data.Count -gt 0) {
        $primeiroUser = $users.data[0]
        
        # Teste 12: Buscar Utilizador por ID
        Test-Endpoint -Nome "Buscar Utilizador por ID" -Metodo "GET" -Url "$baseUrl/api/users.php?id=$($primeiroUser.id)" | Out-Null
    }
}

# Teste 13: Buscar por Tipo (educador)
Test-Endpoint -Nome "Buscar Utilizadores Tipo Educador" -Metodo "GET" -Url "$baseUrl/api/users.php?tipo=educador" | Out-Null

# Teste 14: Buscar por Tipo (dono)
Test-Endpoint -Nome "Buscar Utilizadores Tipo Dono" -Metodo "GET" -Url "$baseUrl/api/users.php?tipo=dono" | Out-Null

Write-Host ""
Write-Host "═══ TESTES DE AUTENTICAÇÃO ═══" -ForegroundColor Magenta
Write-Host ""

# Teste 15: Registar Dono
$donoData = @{
    nome = "Teste Dono $(Get-Date -Format 'HHmmss')"
    email = "teste.dono.$(Get-Date -Format 'HHmmss')@example.com"
    password = "senha123"
    tipo_utilizador = "dono"
    telefone = "912345678"
    distrito = "Lisboa"
} | ConvertTo-Json -Compress

Test-Endpoint -Nome "Registar Novo Dono" -Metodo "POST" -Url "$baseUrl/register.php" -Body $donoData | Out-Null

# Teste 16: Registar Educador
$educadorData = @{
    nome = "Teste Educador $(Get-Date -Format 'HHmmss')"
    email = "teste.educador.$(Get-Date -Format 'HHmmss')@example.com"
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
    anos_experiencia = 3
    biografia = "Educador de teste"
    certificacoes = "Teste APECA"
} | ConvertTo-Json -Compress

Test-Endpoint -Nome "Registar Novo Educador" -Metodo "POST" -Url "$baseUrl/register.php" -Body $educadorData | Out-Null

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║                    RESUMO DOS TESTES                       ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

$percentagem = if ($totalTestes -gt 0) { [math]::Round(($testesPassados / $totalTestes) * 100, 1) } else { 0 }

Write-Host "  Total de Testes: " -NoNewline -ForegroundColor Cyan
Write-Host "$totalTestes" -ForegroundColor White

Write-Host "  Testes Passados: " -NoNewline -ForegroundColor Green
Write-Host "$testesPassados" -ForegroundColor White

Write-Host "  Testes Falhados: " -NoNewline -ForegroundColor Red
Write-Host "$testesFalhados" -ForegroundColor White

Write-Host "  Taxa de Sucesso: " -NoNewline -ForegroundColor Yellow
Write-Host "$percentagem%" -ForegroundColor White

Write-Host ""

if ($testesFalhados -eq 0) {
    Write-Host "🎉 TODOS OS TESTES PASSARAM! 🎉" -ForegroundColor Green
} else {
    Write-Host "⚠️  Alguns testes falharam. Verifique os logs acima." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "💡 Dica: Agora você pode importar a coleção no Postman para testes manuais!" -ForegroundColor Cyan
Write-Host "   Arquivo: postman/AUnidos_Collection.json" -ForegroundColor Gray
Write-Host ""
