<?php
require_once __DIR__ . '/bootstrap.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    setFlash('error', 'Token inválido.');
    redirect('login.php');
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare('SELECT id, nome, email FROM utilizadores WHERE token_reset_password = ? AND token_reset_expiry > NOW() AND ativo = 1');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        setFlash('error', 'Token inválido ou expirado. Solicite nova recuperação.');
        redirect('forgot-password.php');
        exit;
    }
} catch (Exception $e) {
    error_log('Erro ao validar token: ' . $e->getMessage());
    setFlash('error', 'Erro ao processar pedido.');
    redirect('login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pageTitle = 'Nova Password - AUnidos';
    ob_start();
    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3><i class="bi bi-shield-lock"></i> Nova Password</h3>
                            <p class="text-muted">Olá <?php echo e($user['nome']); ?>, defina a sua nova password</p>
                        </div>
                        <form method="POST" action="reset-password.php">
                            <input type="hidden" name="token" value="<?php echo e($token); ?>">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6" autofocus>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>Alterar Password
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

$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (strlen($password) < 6) {
    setFlash('error', 'A password deve ter pelo menos 6 caracteres.');
    redirect('reset-password.php?token=' . $token);
    exit;
}

if ($password !== $confirm) {
    setFlash('error', 'As passwords não coincidem.');
    redirect('reset-password.php?token=' . $token);
    exit;
}

try {
    $db = getDB();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('UPDATE utilizadores SET password_hash = ?, token_reset_password = NULL, token_reset_expiry = NULL WHERE id = ?');
    $stmt->execute([$passwordHash, $user['id']]);
    
    setFlash('success', 'Password alterada com sucesso! Pode fazer login.');
    redirect('login.php');
} catch (Exception $e) {
    error_log('Erro ao alterar password: ' . $e->getMessage());
    setFlash('error', 'Erro ao alterar password. Tente novamente.');
    redirect('reset-password.php?token=' . $token);
}
