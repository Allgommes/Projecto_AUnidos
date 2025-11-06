<?php
/**
 * Script de Teste de Email
 * Testa a configuração do PHPMailer sem criar conta
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/app/Services/EmailService.php';

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Email - AUnidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">🧪 Teste de Configuração de Email</h2>
                        
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            echo '<div class="alert alert-info">
                                    <strong>⏳ Testando envio...</strong>
                                  </div>';
                            
                            try {
                                $emailService = new App\Services\EmailService();
                                
                                // Teste 1: Verificar configurações
                                echo '<div class="alert alert-secondary">';
                                echo '<h5>📋 Configurações SMTP:</h5>';
                                echo '<ul class="mb-0">';
                                echo '<li><strong>Host:</strong> ' . ($_ENV['SMTP_HOST'] ?? 'não configurado') . '</li>';
                                echo '<li><strong>Port:</strong> ' . ($_ENV['SMTP_PORT'] ?? 'não configurado') . '</li>';
                                echo '<li><strong>Username:</strong> ' . ($_ENV['SMTP_USERNAME'] ?? 'não configurado') . '</li>';
                                echo '<li><strong>Password:</strong> ' . (isset($_ENV['SMTP_PASSWORD']) && !empty($_ENV['SMTP_PASSWORD']) ? '✓ Configurada ('. strlen($_ENV['SMTP_PASSWORD']) .' caracteres)' : '❌ Não configurada') . '</li>';
                                echo '<li><strong>From:</strong> ' . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'não configurado') . '</li>';
                                echo '</ul>';
                                echo '</div>';
                                
                                // Teste 2: Enviar email de teste
                                $emailDestino = $_POST['email'] ?? $_ENV['SMTP_USERNAME'];
                                $nome = $_POST['nome'] ?? 'Teste';
                                $token = bin2hex(random_bytes(16));
                                
                                $resultado = $emailService->sendVerificationEmail($emailDestino, $nome, $token);
                                
                                if ($resultado) {
                                    echo '<div class="alert alert-success">
                                            <h5>✅ Email Enviado com Sucesso!</h5>
                                            <p class="mb-0">Verifique a caixa de entrada de: <strong>' . htmlspecialchars($emailDestino) . '</strong></p>
                                            <small class="text-muted">Não esqueça de verificar a pasta SPAM/Lixo Eletrônico</small>
                                          </div>';
                                } else {
                                    echo '<div class="alert alert-warning">
                                            <h5>⚠️ Falha ao Enviar</h5>
                                            <p class="mb-0">Verifique os logs de erro do PHP</p>
                                          </div>';
                                }
                                
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger">
                                        <h5>❌ Erro ao Enviar Email</h5>
                                        <p class="mb-0"><strong>Mensagem:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                                      </div>';
                            }
                        }
                        ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email de Destino</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $_ENV['SMTP_USERNAME'] ?? ''; ?>" required>
                                <small class="text-muted">Email que receberá o teste</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control" name="nome" value="Teste" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                📧 Enviar Email de Teste
                            </button>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="index.php" class="btn btn-outline-secondary">
                                ← Voltar ao Início
                            </a>
                        </div>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6>💡 Dicas de Troubleshooting:</h6>
                            <ul class="small mb-0">
                                <li>Verifique se a App Password do Gmail está correta (16 caracteres sem espaços)</li>
                                <li>Confirme que a verificação em 2 etapas está ativada no Gmail</li>
                                <li>Verifique os logs: <code>C:\xampp\php\logs\php_error_log</code></li>
                                <li>Teste se o XAMPP tem acesso à internet</li>
                                <li>Aguarde alguns segundos, emails podem demorar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
