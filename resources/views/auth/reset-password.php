<?php
$pageTitle = 'Redefinir Password - AUnidos';
$token = $token ?? '';
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill display-4 text-success"></i>
                        <h3 class="mt-3">Nova Password</h3>
                        <p class="text-muted">Escolha uma nova password segura</p>
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

                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="bi bi-check-circle me-2"></i>Alterar Password
                        </button>

                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-2"></i>Voltar ao Login
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
require __DIR__ . '/../layouts/main.php';
?>
