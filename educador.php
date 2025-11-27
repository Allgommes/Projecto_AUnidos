<?php
require_once 'config/database.php';

$educadorId = $_GET['id'] ?? 0;

if (!$educadorId) {
    redirect(SITE_URL . '/buscar-educadores.php');
}

try {
    $db = getDB();
    
    // Obter dados do educador
    $stmt = $db->prepare("
        SELECT 
            e.id,
            u.nome,
            u.email,
            u.telefone,
            u.distrito,
            e.anos_experiencia,
            e.biografia,
            e.certificacoes,
            e.preco_minimo,
            e.preco_maximo,
            e.avaliacao_media,
            e.total_avaliacoes,
            e.foto_perfil,
            GROUP_CONCAT(DISTINCT esp.nome ORDER BY esp.nome SEPARATOR ', ') as especialidades
        FROM educadores e
        JOIN utilizadores u ON e.utilizador_id = u.id
        LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
        LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
        WHERE e.id = ? AND e.aprovado = TRUE AND u.ativo = TRUE
        GROUP BY e.id
    ");
    $stmt->execute([$educadorId]);
    $educador = $stmt->fetch();
    
    if (!$educador) {
        $_SESSION['error_message'] = 'Educador não encontrado.';
        redirect(SITE_URL . '/buscar-educadores.php');
    }
    
    // Obter serviços do educador
    $stmt = $db->prepare("
        SELECT id, nome, descricao, preco, duracao_minutos
        FROM servicos 
        WHERE educador_id = ? AND ativo = TRUE
        ORDER BY preco ASC
    ");
    $stmt->execute([$educadorId]);
    $servicos = $stmt->fetchAll();
    
    // Obter avaliações recentes
    $stmt = $db->prepare("
        SELECT 
            a.nota,
            a.comentario,
            a.data_criacao,
            u.nome as nome_dono
        FROM avaliacoes a
        JOIN agendamentos ag ON a.agendamento_id = ag.id
        JOIN donos d ON a.dono_id = d.id
        JOIN utilizadores u ON d.utilizador_id = u.id
        WHERE a.educador_id = ?
        ORDER BY a.data_criacao DESC
        LIMIT 10
    ");
    $stmt->execute([$educadorId]);
    $avaliacoes = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao obter educador: " . $e->getMessage());
    $_SESSION['error_message'] = 'Erro ao carregar perfil do educador.';
    redirect(SITE_URL . '/buscar-educadores.php');
}

$pageTitle = $educador['nome'] . ' - Educador Canino - AUnidos';

include 'includes/header.php';
?>

<!-- Perfil Header -->
<section class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?php echo $educador['foto_perfil'] ? UPLOAD_URL . 'perfis/' . $educador['foto_perfil'] : 'https://via.placeholder.com/200'; ?>" 
                     alt="<?php echo htmlspecialchars($educador['nome']); ?>" 
                     class="profile-avatar">
            </div>
            <div class="col-md-6">
                <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($educador['nome']); ?></h1>
                <p class="mb-2">
                    <i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($educador['distrito']); ?>
                    <span class="ms-4">
                        <i class="bi bi-calendar me-2"></i><?php echo $educador['anos_experiencia']; ?> anos de experiência
                    </span>
                </p>
                
                <div class="rating mb-3">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $educador['avaliacao_media']): ?>
                            <i class="bi bi-star-fill text-warning fs-5"></i>
                        <?php else: ?>
                            <i class="bi bi-star text-light fs-5"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <span class="ms-2 fs-5">
                        <?php echo number_format($educador['avaliacao_media'], 1); ?> 
                        (<?php echo $educador['total_avaliacoes']; ?> avaliações)
                    </span>
                </div>
                
                <?php if ($educador['especialidades']): ?>
                <div class="mb-3">
                    <?php foreach (explode(', ', $educador['especialidades']) as $esp): ?>
                        <span class="badge bg-light text-dark me-2 fs-6"><?php echo htmlspecialchars($esp); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-3 text-center">
                <div class="price-range mb-3">
                    <div class="h3">
                        €<?php echo number_format($educador['preco_minimo'], 0); ?> - €<?php echo number_format($educador['preco_maximo'], 0); ?>
                    </div>
                    <small>por sessão</small>
                </div>
                <div class="d-grid gap-2">
                    <a href="tel:<?php echo $educador['telefone']; ?>" class="btn btn-light btn-lg">
                        <i class="bi bi-telephone me-2"></i>Ligar Agora
                    </a>
                    <?php if (isLoggedIn() && getUserType() === 'dono'): ?>
                    <button class="btn btn-outline-light btn-lg" data-bs-toggle="modal" data-bs-target="#contactModal">
                        <i class="bi bi-envelope me-2"></i>Enviar Mensagem
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Biografia -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>Sobre Mim
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($educador['biografia'])); ?></p>
                </div>
            </div>
            
            <!-- Certificações -->
            <?php if ($educador['certificacoes']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-award me-2"></i>Certificações e Formação
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($educador['certificacoes'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Serviços -->
            <?php if (!empty($servicos)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>Serviços Oferecidos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($servicos as $servico): ?>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold"><?php echo htmlspecialchars($servico['nome']); ?></h6>
                                    <span class="badge bg-primary">€<?php echo number_format($servico['preco'], 0); ?></span>
                                </div>
                                <p class="small text-muted mb-2"><?php echo htmlspecialchars($servico['descricao']); ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i><?php echo $servico['duracao_minutos']; ?> minutos
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Avaliações -->
            <?php if (!empty($avaliacoes)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-star me-2"></i>Avaliações de Clientes
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?php echo htmlspecialchars($avaliacao['nome_dono']); ?></strong>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $avaliacao['nota']): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star text-muted"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted">
                                <?php echo date('d/m/Y', strtotime($avaliacao['data_criacao'])); ?>
                            </small>
                        </div>
                        <p class="mb-0"><?php echo htmlspecialchars($avaliacao['comentario']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contacto Rápido -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-telephone me-2"></i>Contacto Rápido
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="tel:<?php echo $educador['telefone']; ?>" class="btn btn-success">
                            <i class="bi bi-telephone me-2"></i><?php echo $educador['telefone']; ?>
                        </a>
                        <?php if (isLoggedIn()): ?>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-envelope me-2"></i>Enviar Mensagem
                        </button>
                        <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login para Contactar
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Resumo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Resumo
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Experiência:</strong> <?php echo $educador['anos_experiencia']; ?> anos
                        </li>
                        <li class="mb-2">
                            <strong>Localização:</strong> <?php echo htmlspecialchars($educador['distrito']); ?>
                        </li>
                        <li class="mb-2">
                            <strong>Avaliação:</strong> 
                            <?php echo number_format($educador['avaliacao_media'], 1); ?>/5 
                            (<?php echo $educador['total_avaliacoes']; ?> avaliações)
                        </li>
                        <li class="mb-2">
                            <strong>Preços:</strong> €<?php echo number_format($educador['preco_minimo'], 0); ?> - €<?php echo number_format($educador['preco_maximo'], 0); ?>
                        </li>
                        <li class="mb-0">
                            <strong>Serviços:</strong> <?php echo count($servicos); ?> disponíveis
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Outros Educadores -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>Outros Educadores
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Explore outros educadores na sua região.</p>
                    <a href="buscar-educadores.php?distrito=<?php echo urlencode($educador['distrito']); ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-search me-2"></i>Ver Mais em <?php echo htmlspecialchars($educador['distrito']); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Contacto -->
<?php if (isLoggedIn()): ?>
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope me-2"></i>Contactar <?php echo htmlspecialchars($educador['nome']); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <input type="hidden" name="educador_id" value="<?php echo $educador['id']; ?>">
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto</label>
                        <select class="form-select" id="assunto" name="assunto" required>
                            <option value="">Selecione o assunto</option>
                            <option value="informacoes">Pedido de Informações</option>
                            <option value="agendamento">Agendamento de Sessão</option>
                            <option value="preco">Consulta de Preços</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Mensagem</label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="4" 
                                  placeholder="Descreva as suas necessidades..." required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle me-2"></i>
                            O educador receberá a sua mensagem por email e poderá contactá-lo diretamente.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="contactForm" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Enviar Mensagem
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
<?php if (isLoggedIn()): ?>
// Submissão do formulário de contacto
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('ajax/enviar-mensagem.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mensagem enviada com sucesso!');
            bootstrap.Modal.getInstance(document.getElementById('contactModal')).hide();
            this.reset();
        } else {
            alert('Erro ao enviar mensagem: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao enviar mensagem. Tente novamente.');
    });
});
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>