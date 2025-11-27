<?php
$pageTitle = 'Recuperar Password - AUnidos';
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-key-fill display-4 text-primary"></i>
                        <h3 class="mt-3">Recuperar Password</h3>
                        <p class="text-muted">Introduza o seu email para receber instruções</p>
                    </div>

                    <form method="POST" action="forgot-password.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-envelope me-2"></i>Enviar Instruções
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
