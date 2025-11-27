
Agora vou criar um script PowerShell para executar os testes automaticamente:

```powershell
# Script de Teste Automático da API AUnidos
# Uso: .\postman\testar_api.ps1

Write-Host "🚀 Iniciando testes da API AUnidos..." -ForegroundColor Green
Write-Host ""

$baseUrl = "http://localhost/Projecto_AUnidos"
$headers = @{
    "Content-Type" = "application/json; charset=utf-8"
}

# Teste 1: Conexão
Write-Host "📡 Teste 1: Verificando conexão com o banco..." -ForegroundColor Cyan
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/test-connection.php" -Method Get
    Write-Host "✅ Conexão OK" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "❌ Erro na conexão: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 2: Registar Dono
Write-Host "👤 Teste 2: Registando dono..." -ForegroundColor Cyan
$dono = @{
    nome = "Teste Dono $(Get-Date -Format 'HHmmss')"
    email = "dono.teste.$(Get-Date -Format 'HHmmss')@example.com"
    password = "senha123"
    tipo_utilizador = "dono"
    telefone = "912345678"
    distrito = "Lisboa"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/register.php" -Method Post -Body $dono -Headers $headers
    Write-Host "✅ Dono registado com sucesso" -ForegroundColor Green
    $donoEmail = ($dono | ConvertFrom-Json).email
    Write-Host "   Email: $donoEmail" -ForegroundColor Yellow
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "❌ Erro ao registar dono: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 3: Registar Educador
Write-Host "🎓 Teste 3: Registando educador..." -ForegroundColor Cyan
$educador = @{
    nome = "Teste Educador $(Get-Date -Format 'HHmmss')"
    email = "educador.teste.$(Get-Date -Format 'HHmmss')@example.com"
    password = "senha123"
    tipo_utilizador = "educador"
    telefone = "918765432"
    distrito = "Porto"
    anos_experiencia = 5
    biografia = "Educador de testes"
    certificacoes = "Teste APECA"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/register.php" -Method Post -Body $educador -Headers $headers
    Write-Host "✅ Educador registado com sucesso" -ForegroundColor Green
    $educadorEmail = ($educador | ConvertFrom-Json).email
    Write-Host "   Email: $educadorEmail" -ForegroundColor Yellow
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "❌ Erro ao registar educador: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 4: Login
Write-Host "🔐 Teste 4: Testando login..." -ForegroundColor Cyan
$login = @{
    email = $donoEmail
    password = "senha123"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/login.php" -Method Post -Body $login -Headers $headers
    Write-Host "✅ Login realizado com sucesso" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "❌ Erro no login: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 5: Listar Educadores
Write-Host "📋 Teste 5: Listando educadores..." -ForegroundColor Cyan
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/educadores.php" -Method Get
    Write-Host "✅ Educadores listados: $($response.data.Count)" -ForegroundColor Green
    $response.data | Select-Object -First 3 | ConvertTo-Json -Depth 2
} catch {
    Write-Host "❌ Erro ao listar educadores: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 6: Criar Serviço
Write-Host "💼 Teste 6: Criando serviço..." -ForegroundColor Cyan
$servico = @{
    educador_id = 1
    nome = "Teste Serviço $(Get-Date -Format 'HHmmss')"
    descricao = "Serviço de teste automatizado"
    preco_hora = 25.50
    duracao_minutos = 60
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/servicos.php" -Method Post -Body $servico -Headers $headers
    Write-Host "✅ Serviço criado com sucesso" -ForegroundColor Green
    $servicoId = $response.data.id
    Write-Host "   ID do serviço: $servicoId" -ForegroundColor Yellow
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "❌ Erro ao criar serviço: $_" -ForegroundColor Red
}
Write-Host ""

# Teste 7: Listar Serviços
Write-Host "📋 Teste 7: Listando serviços..." -ForegroundColor Cyan
try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/servicos.php" -Method Get
    Write-Host "✅ Serviços listados: $($response.data.Count)" -ForegroundColor Green
    $response.data | Select-Object -First 3 | ConvertTo-Json -Depth 2
} catch {
    Write-Host "❌ Erro ao listar serviços: $_" -ForegroundColor Red
}
Write-Host ""

# Resumo
Write-Host "=" * 60 -ForegroundColor Green
Write-Host "✅ TESTES CONCLUÍDOS" -ForegroundColor Green
Write-Host "=" * 60 -ForegroundColor Green
Write-Host ""
Write-Host "📊 Resumo dos dados criados:" -ForegroundColor Cyan
Write-Host "   Dono: $donoEmail" -ForegroundColor Yellow
Write-Host "   Educador: $educadorEmail" -ForegroundColor Yellow
if ($servicoId) {
    Write-Host "   Serviço ID: $servicoId" -ForegroundColor Yellow
}
Write-Host ""
Write-Host "💡 Para ver os dados no banco:" -ForegroundColor Cyan
Write-Host '   & "C:\xampp\mysql\bin\mysql.exe" -u root aunidos -e "SELECT * FROM utilizadores ORDER BY id DESC LIMIT 5;"' -ForegroundColor Gray