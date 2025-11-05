<?php
require_once 'config/database.php';
require_once 'src/classes/User.php';

$pageTitle = 'Redefinir Password - AUnidos';
$token = $_GET['token'] ?? '';
$message = '';
$success = false;

if (empty($token)) {
    redirect(SITE_URL . 'login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    $result = $user->resetPassword($token, $_POST['password']);
    
    if ($result['success']) {
        $success = true;
        $message = 'Password redefinida com sucesso! Pode agora fazer login.';
    } else {
        $message = $result['error'];
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h3 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Nova Password
                    </h3>
                    <p class="mb-0 mt-2 opacity-75">Defina uma nova password para a sua conta</p>
                </div>
                
                <div class="card-body p-5">
                    <?php if ($message): ?>
                        <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                            <i class="bi <?php echo $success ? 'bi-check-circle' : 'bi-exclamation-triangle'; ?> me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Fazer Login
                            </a>
                        </div>
                    <?php else: ?>
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="password" class="form-label">Nova Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Mínimo 6 caracteres" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        A password deve ter pelo menos 6 caracteres.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nova Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Repita a password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        As passwords não coincidem.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Indicador de força da password -->
                            <div class="mb-3">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="form-text text-muted" id="passwordStrengthText">
                                    Força da password
                                </small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg me-2"></i>Redefinir Password
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">
                            <a href="login.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Voltar ao Login
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    // Toggle password visibility
    function setupPasswordToggle(buttonId, inputId) {
        const button = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        
        button.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }
    
    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('toggleConfirmPassword', 'confirm_password');
    
    // Verificar força da password
    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength += 20;
        if (password.length >= 8) strength += 20;
        if (/[a-z]/.test(password)) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        
        return strength;
    }
    
    password.addEventListener('input', function() {
        const strength = checkPasswordStrength(this.value);
        strengthBar.style.width = strength + '%';
        
        let className = 'bg-danger';
        let text = 'Fraca';
        
        if (strength >= 80) {
            className = 'bg-success';
            text = 'Forte';
        } else if (strength >= 60) {
            className = 'bg-warning';
            text = 'Média';
        } else if (strength >= 40) {
            className = 'bg-info';
            text = 'Razoável';
        }
        
        strengthBar.className = 'progress-bar ' + className;
        strengthText.textContent = 'Força da password: ' + text;
    });
    
    // Validação de confirmação de password
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('As passwords não coincidem');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
    
    // Validação Bootstrap
    window.addEventListener('load', function() {
        const forms = document.getElementsByClassName('needs-validation');
        const validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        // Auto-focus na password
        if (password) {
            password.focus();
        }
    }, false);
})();
</script>

<?php include 'includes/footer.php'; ?>