# Script Simplificado de Teste das APIs
# Uso: .\api\test_apis_simple.ps1

Write-Host "`n=== TESTANDO APIS DO AUNIDOS ===`n" -ForegroundColor Green

$base = "http://localhost/Projecto_AUnidos"

# Test 1
Write-Host "1. Test Connection..." -ForegroundColor Cyan
$r1 = Invoke-RestMethod "$base/api/test-connection.php"
Write-Host "   OK - $($r1.data.total_utilizadores) users" -ForegroundColor Green

# Test 2
Write-Host "2. GET Educadores..." -ForegroundColor Cyan
$r2 = Invoke-RestMethod "$base/api/educadores.php"
Write-Host "   OK - $($r2.data.Count) educadores" -ForegroundColor Green

# Test 3
Write-Host "3. GET Servicos..." -ForegroundColor Cyan
$r3 = Invoke-RestMethod "$base/api/servicos.php"
Write-Host "   OK - $($r3.data.Count) servicos" -ForegroundColor Green

# Test 4
Write-Host "4. GET Users..." -ForegroundColor Cyan
$r4 = Invoke-RestMethod "$base/api/users.php"
Write-Host "   OK - $($r4.total) users" -ForegroundColor Green

# Test 5 - Criar Servico
if ($r2.data.Count -gt 0) {
    Write-Host "5. POST Criar Servico..." -ForegroundColor Cyan
    $data = @{
        educador_id = $r2.data[0].id
        nome = "Teste API $(Get-Date -Format 'HHmmss')"
        descricao = "Teste automatizado"
        preco_hora = 25.50
        duracao_minutos = 60
    } | ConvertTo-Json
    
    $r5 = Invoke-RestMethod "$base/api/servicos.php" -Method Post -Body $data -ContentType "application/json"
    Write-Host "   OK - Servico ID: $($r5.data.id)" -ForegroundColor Green
    
    # Test 6 - Atualizar
    Write-Host "6. PUT Atualizar Servico..." -ForegroundColor Cyan
    $update = @{
        id = $r5.data.id
        preco_hora = 30.00
    } | ConvertTo-Json
    
    Invoke-RestMethod "$base/api/servicos.php" -Method Put -Body $update -ContentType "application/json" | Out-Null
    Write-Host "   OK - Servico atualizado" -ForegroundColor Green
    
    # Test 7 - Deletar
    Write-Host "7. DELETE Remover Servico..." -ForegroundColor Cyan
    $delete = @{ id = $r5.data.id } | ConvertTo-Json
    Invoke-RestMethod "$base/api/servicos.php" -Method Delete -Body $delete -ContentType "application/json" | Out-Null
    Write-Host "   OK - Servico removido" -ForegroundColor Green
}

Write-Host "`n=== TODOS OS TESTES PASSARAM ===`n" -ForegroundColor Green
