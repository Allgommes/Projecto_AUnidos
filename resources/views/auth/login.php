<?php
$pageTitle = 'Login - AUnidos';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3><i class="bi bi-box-arrow-in-right"></i> Login</h3>
                        <p class="text-muted">Bem-vindo de volta ao AUnidos</p>
                    </div>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Lembrar-me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                        </button>

                        <div class="text-center">
                            <a href="forgot-password.php" class="text-decoration-none">Esqueceu a password?</a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">Ainda não tem conta?</p>
                            <a href="register.php" class="btn btn-outline-primary mt-2">
                                <i class="bi bi-person-plus me-2"></i>Criar Conta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

