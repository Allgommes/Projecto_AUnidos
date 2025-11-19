<?php
require_once __DIR__ . '/bootstrap.php';

// Verificar se está logado e é educador
if (!isLoggedIn() || getUserType() !== 'educador') {
    redirect(SITE_URL . '/dashboard.php');
}

$pageTitle = 'Meus Serviços - AUnidos';
$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Obter ID do educador
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM educadores WHERE utilizador_id = ?");
    $stmt->execute([$userId]);
    $educador = $stmt->fetch();
    $educadorId = $educador['id'];
} catch (Exception $e) {
    redirect(SITE_URL . '/dashboard.php');
}

// Processar criação/edição de serviço
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        
        $nome = sanitizeInput($_POST['nome']);
        $descricao = sanitizeInput($_POST['descricao']);
            // Usar preco_hora conforme schema
            $preco_hora = floatval($_POST['preco']);
            $preco_hora = $preco_hora > 0 ? number_format($preco_hora, 2, '.', '') : 0;
        $duracao = intval($_POST['duracao']);
        
        if (empty($nome) || empty($descricao) || $preco_hora < 0.01 || $duracao <= 0) {
            $errors[] = 'Todos os campos são obrigatórios. O preço deve ser pelo menos €0.01 e a duração deve ser positiva.';
        } else {
            if (isset($_POST['servico_id']) && !empty($_POST['servico_id'])) {
                // Editar serviço existente
                    $stmt = $db->prepare("
                        UPDATE servicos 
                        SET nome = ?, descricao = ?, preco_hora = ?, duracao_minutos = ?
                        WHERE id = ? AND educador_id = ?
                    ");
                    $stmt->execute([$nome, $descricao, $preco_hora, $duracao, (int)$_POST['servico_id'], $educadorId]);
                $_SESSION['success_message'] = 'Serviço atualizado com sucesso!';
            } else {
                // Criar novo serviço
                    $stmt = $db->prepare("
                        INSERT INTO servicos (educador_id, nome, descricao, preco_hora, duracao_minutos)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$educadorId, $nome, $descricao, $preco_hora, $duracao]);
                $_SESSION['success_message'] = 'Serviço criado com sucesso!';
            }
            $success = true;
        }
        
    } catch (Exception $e) {
        error_log("Erro ao processar serviço: " . $e->getMessage());
        $errors[] = 'Erro ao processar serviço. Tente novamente.';
    }
}

// Processar eliminação de serviço
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM servicos WHERE id = ? AND educador_id = ?");
        $stmt->execute([$_GET['eliminar'], $educadorId]);
        $_SESSION['success_message'] = 'Serviço eliminado com sucesso!';
        redirect(SITE_URL . '/meus-servicos.php');
    } catch (Exception $e) {
        error_log("Erro ao eliminar serviço: " . $e->getMessage());
        $errors[] = 'Erro ao eliminar serviço.';
    }
}

// Carregar serviços do educador
try {
    $db = getDB();
    $stmt = $db->prepare("
           SELECT id, nome, descricao, preco_hora, duracao_minutos, ativo
        FROM servicos 
        WHERE educador_id = ?
        ORDER BY nome
    ");
    $stmt->execute([$educadorId]);
    $servicos = $stmt->fetchAll();
} catch (Exception $e) {
    $servicos = [];
}

// Carregar dados do serviço para edição
$servicoEdicao = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("
              SELECT id, nome, descricao, preco_hora, duracao_minutos
            FROM servicos 
            WHERE id = ? AND educador_id = ?
        ");
        $stmt->execute([$_GET['editar'], $educadorId]);
        $servicoEdicao = $stmt->fetch();
    } catch (Exception $e) {
        $servicoEdicao = null;
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 fw-bold">
                        <i class="bi bi-briefcase me-2 text-primary"></i>Meus Serviços
                    </h1>
                    <p class="text-muted">Gerencie os serviços que oferece aos donos</p>
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
            
            <div class="row">
                <!-- Formulário de Serviço -->
                <div class="col-lg-5">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>
                                <?php echo $servicoEdicao ? 'Editar Serviço' : 'Novo Serviço'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <?php if ($servicoEdicao): ?>
                                    <input type="hidden" name="servico_id" value="<?php echo $servicoEdicao['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Serviço</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?php echo $servicoEdicao ? htmlspecialchars($servicoEdicao['nome']) : ''; ?>" 
                                           placeholder="Ex: Treino de Obediência Básica" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="descricao" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="4" 
                                              placeholder="Descreva o que inclui neste serviço..." required><?php echo $servicoEdicao ? htmlspecialchars($servicoEdicao['descricao']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label for="preco" class="form-label">Preço (€)</label>
                                        <input type="number" class="form-control" id="preco" name="preco" 
                                               value="<?php echo $servicoEdicao ? $servicoEdicao['preco_hora'] : ''; ?>" 
                                               min="0.01" step="0.01" placeholder="Ex: 25.00" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="duracao" class="form-label">Duração (min)</label>
                                        <input type="number" class="form-control" id="duracao" name="duracao" 
                                               value="<?php echo $servicoEdicao ? $servicoEdicao['duracao_minutos'] : ''; ?>" 
                                               min="15" step="15" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>
                                        <?php echo $servicoEdicao ? 'Atualizar Serviço' : 'Criar Serviço'; ?>
                                    </button>
                                    <?php if ($servicoEdicao): ?>
                                        <a href="meus-servicos.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-lg me-2"></i>Cancelar Edição
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Lista de Serviços -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Serviços Atuais
                            </h5>
                            <span class="badge bg-primary"><?php echo count($servicos); ?> serviços</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($servicos)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-briefcase-fill text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nenhum serviço criado</h5>
                                    <p class="text-muted">Crie o seu primeiro serviço para começar a receber reservas.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($servicos as $servico): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($servico['nome']); ?></h6>
                                                    <p class="mb-2 text-muted small"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                                                    <div class="d-flex gap-3">
                                                        <span class="badge bg-success">€<?php echo number_format($servico['preco_hora'], 2); ?></span>
                                                        <span class="badge bg-info"><?php echo $servico['duracao_minutos']; ?> min</span>
                                                        <span class="badge <?php echo $servico['ativo'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo $servico['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <a href="?editar=<?php echo $servico['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?eliminar=<?php echo $servico['id']; ?>" 
                                                       class="btn btn-outline-danger"
                                                       onclick="return confirm('Tem certeza que quer eliminar este serviço?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Dicas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightbulb me-2"></i>Dicas para Serviços
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="small mb-0">
                                <li>Use nomes descritivos e claros para os seus serviços</li>
                                <li>Inclua detalhes sobre o que está incluído no serviço</li>
                                <li>Defina preços competitivos baseados na sua experiência</li>
                                <li>Especifique durações realistas para cada sessão</li>
                                <li>Mantenha os seus serviços atualizados</li>
                            </ul>
                        </div>
                    </div>
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
</script>

<?php include 'includes/footer.php'; ?>