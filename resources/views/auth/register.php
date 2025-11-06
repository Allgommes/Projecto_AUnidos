<?php
$pageTitle = 'Registar - AUnidos';
$distritos = $distritos ?? [
    'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
    'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
    'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'
];
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h3><i class="bi bi-person-plus"></i> Criar Conta</h3>
                        <p class="text-muted">Junte-se à comunidade AUnidos</p>
                    </div>

                    <form method="POST" action="register.php" id="registerForm">
                        <!-- Tipo de Utilizador -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tipo de Conta *</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100 cursor-pointer" onclick="selectUserType('dono')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-heart-fill display-4 text-primary mb-3"></i>
                                            <h5>Dono de Cão</h5>
                                            <p class="small text-muted">Procuro educadores para o meu cão</p>
                                            <input type="radio" name="tipo_utilizador" value="dono" id="tipo_dono" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100 cursor-pointer" onclick="selectUserType('educador')">
                                        <div class="card-body text-center">
                                            <i class="bi bi-award-fill display-4 text-success mb-3"></i>
                                            <h5>Educador Canino</h5>
                                            <p class="small text-muted">Ofereço serviços de treino</p>
                                            <input type="radio" name="tipo_utilizador" value="educador" id="tipo_educador" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Pessoais -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo e(old('nome')); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo e(old('email')); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo e(old('telefone')); ?>" placeholder="+351 123 456 789">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="distrito" class="form-label">Distrito *</label>
                                <select class="form-select" id="distrito" name="distrito" required>
                                    <option value="">Selecione o distrito</option>
                                    <?php foreach ($distritos as $distrito): ?>
                                        <option value="<?php echo $distrito; ?>" <?php echo (old('distrito') === $distrito) ? 'selected' : ''; ?>>
                                            <?php echo $distrito; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <!-- Termos -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                Aceito os <a href="#" class="text-decoration-none">Termos e Condições</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Criar Conta
                        </button>

                        <div class="text-center">
                            <p class="mb-0">Já tem conta? <a href="login.php" class="text-decoration-none">Fazer Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectUserType(type) {
    document.getElementById('tipo_' + type).checked = true;
}
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.card:has(input:checked) {
    border-color: #4CAF50;
    background-color: #f1f8f4;
}
</style>

