<?php
require_once __DIR__ . '/bootstrap.php';

// GET: mostrar formulário
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pageTitle = 'Recuperar Password - AUnidos';
    ob_start();
    ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3><i class="bi bi-key"></i> Recuperar Password</h3>
                            <p class="text-muted">Insira o seu email para receber instruções</p>
                        </div>

                        <form method="POST" action="forgot-password.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-envelope me-2"></i>Enviar Email de Recuperação
                            </button>

                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Voltar ao Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/resources/views/layouts/main.php';
    exit;
}

// POST: processar pedido
$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Por favor, insira um email válido.');
    redirect('forgot-password.php');
    exit;
}

try {
    $db = getDB();
    
    // Verificar se email existe
    $stmt = $db->prepare('SELECT id, nome, email FROM utilizadores WHERE email = ? AND ativo = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Gerar token de reset
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Guardar token no banco
        $stmt = $db->prepare('
            UPDATE utilizadores 
            SET token_reset_password = ?, token_reset_expiry = ? 
            WHERE id = ?
        ');
        $stmt->execute([$token, $expiry, $user['id']]);
        
        // Enviar email
        require_once __DIR__ . '/app/Services/EmailService.php';
        $emailService = new App\Services\EmailService();
        $emailService->sendPasswordResetEmail($user['email'], $user['nome'], $token);
    }
    
    // Sempre mostrar mesma mensagem (segurança)
    setFlash('success', 'Se o email existir na nossa base de dados, receberá instruções de recuperação.');
    redirect('login.php');
    
} catch (Exception $e) {
    error_log('Erro ao processar forgot-password: ' . $e->getMessage());
    setFlash('error', 'Ocorreu um erro. Tente novamente.');
    redirect('forgot-password.php');
}
?>
