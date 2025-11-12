<?php
require_once __DIR__ . '/bootstrap.php';

// Verificar se está logado
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Meu Perfil - AUnidos';
$userType = getUserType();
$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Processar upload de foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $uploadDir = UPLOAD_PATH . 'perfis/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $file = $_FILES['foto_perfil'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                try {
                    $db = getDB();
                    
                    // CORREÇÃO: Apenas educadores têm campo foto_perfil na BD
                    // Tabela 'donos' não possui este campo (apenas id e utilizador_id)
                    if ($userType === 'educador') {
                        $stmt = $db->prepare("UPDATE educadores SET foto_perfil = ? WHERE utilizador_id = ?");
                        $stmt->execute([$filename, $userId]);
                        $_SESSION['success_message'] = 'Foto atualizada com sucesso!';
                    } else {
                        // REMOVIDO: UPDATE donos SET foto_perfil (campo não existe)
                        // Donos usam apenas placeholder para foto
                        $_SESSION['success_message'] = 'Upload realizado! (Donos não possuem foto de perfil na BD)';
                    }
                    
                } catch (Exception $e) {
                    error_log("Erro ao atualizar foto: " . $e->getMessage());
                    $errors[] = 'Erro ao salvar foto na base de dados.';
                }
            } else {
                $errors[] = 'Erro ao fazer upload da foto.';
            }
        } else {
            $errors[] = 'Formato de arquivo inválido ou arquivo muito grande.';
        }
    }
}

// Processar atualização de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_FILES['foto_perfil'])) {
    try {
        $db = getDB();
        
        // Atualizar dados básicos do utilizador
        $stmt = $db->prepare("
            UPDATE utilizadores 
            SET nome = ?, telefone = ?, distrito = ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['nome'],
            $_POST['telefone'],
            $_POST['distrito'],
            $userId
        ]);
        
        if ($userType === 'educador') {
            // CORREÇÃO: Removidos campos preco_minimo e preco_maximo (não existem na tabela educadores)
            // Campos disponíveis: utilizador_id, biografia, anos_experiencia, certificacoes, foto_perfil, aprovado, avaliacao_media, total_avaliacoes
            $stmt = $db->prepare("
                UPDATE educadores 
                SET anos_experiencia = ?, biografia = ?, certificacoes = ?
                WHERE utilizador_id = ?
            ");
            $stmt->execute([
                $_POST['anos_experiencia'] ?? 0,
                $_POST['biografia'] ?? '',
                $_POST['certificacoes'] ?? '',
                // REMOVIDO: $_POST['preco_minimo'], $_POST['preco_maximo']
                $userId
            ]);
            
            // Atualizar especialidades
            $educadorId = $db->query("SELECT id FROM educadores WHERE utilizador_id = $userId")->fetch()['id'];
            
            // Remover especialidades antigas
            $stmt = $db->prepare("DELETE FROM educador_especialidades WHERE educador_id = ?");
            $stmt->execute([$educadorId]);
            
            // Adicionar novas especialidades
            if (!empty($_POST['especialidades'])) {
                $stmt = $db->prepare("INSERT INTO educador_especialidades (educador_id, especialidade_id) VALUES (?, ?)");
                foreach ($_POST['especialidades'] as $especialidadeId) {
                    $stmt->execute([$educadorId, $especialidadeId]);
                }
            }
        }
        
        $_SESSION['success_message'] = 'Perfil atualizado com sucesso!';
        $success = true;
        
    } catch (Exception $e) {
        error_log("Erro ao atualizar perfil: " . $e->getMessage());
        $errors[] = 'Erro ao atualizar perfil. Tente novamente.';
    }
}

// Carregar dados do perfil
try {
    $db = getDB();
    
    if ($userType === 'educador') {
        $stmt = $db->prepare("
            SELECT u.*, e.anos_experiencia, e.biografia, e.certificacoes, 
                   e.preco_minimo, e.preco_maximo, e.foto_perfil, e.id as educador_id
            FROM utilizadores u 
            JOIN educadores e ON u.id = e.utilizador_id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $perfil = $stmt->fetch();
        
        // Carregar especialidades do educador
        $stmt = $db->prepare("
            SELECT especialidade_id 
            FROM educador_especialidades 
            WHERE educador_id = ?
        ");
        $stmt->execute([$perfil['educador_id']]);
        $especialidadesSelecionadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
    } else {
        $stmt = $db->prepare("
            SELECT u.*, d.foto_perfil 
            FROM utilizadores u 
            JOIN donos d ON u.id = d.utilizador_id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $perfil = $stmt->fetch();
        $especialidadesSelecionadas = [];
    }
    
    // Carregar todas as especialidades
    $stmt = $db->query("SELECT id, nome FROM especialidades ORDER BY nome");
    $todasEspecialidades = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao carregar perfil: " . $e->getMessage());
    redirect(SITE_URL . '/dashboard.php');
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold">
                        <i class="bi bi-person me-2 text-primary"></i>Meu Perfil
                    </h1>
                    <p class="text-muted">Gerencie as suas informações pessoais</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard
                </a>
            </div>
            
            <!-- Mensagens -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Foto de Perfil -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-camera me-2"></i>Foto de Perfil
                    </h5>
                </div>
                <div class="card-body text-center">
                    <img src="<?php echo $perfil['foto_perfil'] ? UPLOAD_URL . 'perfis/' . $perfil['foto_perfil'] : 'https://via.placeholder.com/150'; ?>" 
                         alt="Foto de perfil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <form method="POST" enctype="multipart/form-data" class="d-inline">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="foto_perfil" accept="image/*" required>
                            <div class="form-text">Formatos aceites: JPG, PNG. Máximo 5MB.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Atualizar Foto
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Dados Pessoais -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>Informações Pessoais
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- Dados Básicos -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo htmlspecialchars($perfil['nome']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?php echo htmlspecialchars($perfil['email']); ?>" disabled>
                                <div class="form-text">O email não pode ser alterado.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" 
                                       value="<?php echo htmlspecialchars($perfil['telefone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="distrito" class="form-label">Distrito</label>
                                <select class="form-select" id="distrito" name="distrito" required>
                                    <?php
                                    $distritos = ['Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
                                                 'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
                                                 'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'];
                                    foreach ($distritos as $d):
                                    ?>
                                        <option value="<?php echo $d; ?>" <?php echo ($perfil['distrito'] === $d) ? 'selected' : ''; ?>>
                                            <?php echo $d; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Campos específicos para educadores -->
                        <?php if ($userType === 'educador'): ?>
                        <hr class="my-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-mortarboard me-2"></i>Informações Profissionais
                        </h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="anos_experiencia" class="form-label">Anos de Experiência</label>
                                <input type="number" class="form-control" id="anos_experiencia" name="anos_experiencia" 
                                       value="<?php echo htmlspecialchars($perfil['anos_experiencia']); ?>" min="0" max="50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Especialidades</label>
                                <div class="row">
                                    <?php foreach ($todasEspecialidades as $esp): ?>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="esp_<?php echo $esp['id']; ?>" 
                                                   name="especialidades[]" 
                                                   value="<?php echo $esp['id']; ?>"
                                                   <?php echo in_array($esp['id'], $especialidadesSelecionadas) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="esp_<?php echo $esp['id']; ?>">
                                                <?php echo htmlspecialchars($esp['nome']); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="preco_minimo" class="form-label">Preço Mínimo (€)</label>
                                <input type="number" class="form-control" id="preco_minimo" name="preco_minimo" 
                                       value="<?php echo htmlspecialchars($perfil['preco_minimo']); ?>" min="0" step="0.01" placeholder="Ex: 20.00">
                            </div>
                            <div class="col-md-6">
                                <label for="preco_maximo" class="form-label">Preço Máximo (€)</label>
                                <input type="number" class="form-control" id="preco_maximo" name="preco_maximo" 
                                       value="<?php echo htmlspecialchars($perfil['preco_maximo']); ?>" min="0" step="0.01" placeholder="Ex: 50.00">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografia</label>
                            <textarea class="form-control" id="biografia" name="biografia" rows="4" 
                                      placeholder="Descreva a sua experiência, abordagem de treino e filosofia..."><?php echo htmlspecialchars($perfil['biografia']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="certificacoes" class="form-label">Certificações e Formação</label>
                            <textarea class="form-control" id="certificacoes" name="certificacoes" rows="3" 
                                      placeholder="Liste as suas certificações, cursos e formações relevantes..."><?php echo htmlspecialchars($perfil['certificacoes']); ?></textarea>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="dashboard.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Alterar Password -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock me-2"></i>Segurança
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Para alterar a sua password, utilize a opção de recuperação de password.</p>
                    <a href="forgot-password.php" class="btn btn-outline-warning">
                        <i class="bi bi-key me-2"></i>Alterar Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validação de formulário
(function() {
    'use strict';
    
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
    }, false);
})();

// Validação de preços
document.getElementById('preco_minimo')?.addEventListener('input', function() {
    const precoMax = document.getElementById('preco_maximo');
    if (precoMax.value && parseFloat(this.value) > parseFloat(precoMax.value)) {
        precoMax.value = this.value;
    }
});
</script>

<?php include 'includes/footer.php'; ?>