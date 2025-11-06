<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = 'AUnidos - Conectando Donos e Educadores Caninos';

try {
    $db = getDB();
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM educadores e JOIN utilizadores u ON e.utilizador_id = u.id WHERE e.aprovado = TRUE AND u.ativo = TRUE");
    $totalEducadores = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM donos d JOIN utilizadores u ON d.utilizador_id = u.id WHERE u.ativo = TRUE");
    $totalDonos = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $db->query("SELECT u.nome, e.avaliacao_media, e.total_avaliacoes, u.distrito, e.foto_perfil, e.id, e.biografia FROM educadores e JOIN utilizadores u ON e.utilizador_id = u.id WHERE e.aprovado = TRUE AND u.ativo = TRUE ORDER BY e.avaliacao_media DESC LIMIT 6");
    $educadoresDestaque = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro: " . $e->getMessage());
    $totalEducadores = 0;
    $totalDonos = 0;
    $educadoresDestaque = [];
}

ob_start(); // ob_start buffering
?>

<div class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Conectamos <span class="text-warning">donos</span> e <span class="text-warning">educadores</span> caninos
                </h1>
                <p class="lead mb-4">
                    Encontre o educador ideal para o seu cão ou ofereça os seus serviços de treino.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="buscar-educadores.php" class="btn btn-light btn-lg">
                        <i class="bi bi-search me-2"></i>Buscar Educadores
                    </a>
                    <?php if (!isAuthenticated()): ?>
                    <a href="register.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus me-2"></i>Registar Agora
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=600&h=400&fit=crop" alt="Cão" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</div>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-award-fill display-3 text-primary mb-3"></i>
                        <h3 class="fw-bold"><?php echo $totalEducadores; ?>+</h3>
                        <p class="text-muted mb-0">Educadores Certificados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-heart-fill display-3 text-danger mb-3"></i>
                        <h3 class="fw-bold"><?php echo $totalDonos; ?>+</h3>
                        <p class="text-muted mb-0">Donos Satisfeitos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-star-fill display-3 text-warning mb-3"></i>
                        <h3 class="fw-bold">4.8/5</h3>
                        <p class="text-muted mb-0">Avaliação Média</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($educadoresDestaque)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5"><i class="bi bi-star-fill text-warning me-2"></i>Educadores em Destaque</h2>
        <div class="row">
            <?php foreach ($educadoresDestaque as $edu): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <?php if ($edu['foto_perfil']): ?>
                                <img src="<?php echo htmlspecialchars(UPLOAD_URL . $edu['foto_perfil']); ?>" alt="Educador" class="rounded-circle" style="width:100px;height:100px;object-fit:cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:100px;height:100px;font-size:2rem;">
                                    <?php echo strtoupper(substr($edu['nome'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h5><?php echo htmlspecialchars($edu['nome']); ?></h5>
                        <p class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($edu['distrito']); ?></p>
                        <?php if ($edu['avaliacao_media']): ?>
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> <?php echo number_format($edu['avaliacao_media'], 1); ?></span>
                            <small class="text-muted">(<?php echo $edu['total_avaliacoes']; ?>)</small>
                        </div>
                        <?php endif; ?>
                        <a href="educador.php?id=<?php echo $edu['id']; ?>" class="btn btn-outline-primary w-100">Ver Perfil</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="buscar-educadores.php" class="btn btn-primary btn-lg">Ver Todos <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Como Funciona</h2>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <i class="bi bi-person-plus-fill display-1 text-primary mb-3"></i>
                <h4>1. Registe-se</h4>
                <p class="text-muted">Crie a sua conta gratuita</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="bi bi-search display-1 text-primary mb-3"></i>
                <h4>2. Procure</h4>
                <p class="text-muted">Encontre educadores na sua área</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="bi bi-calendar-check-fill display-1 text-primary mb-3"></i>
                <h4>3. Agende</h4>
                <p class="text-muted">Marque o treino do seu cão</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <i class="bi bi-star-fill display-1 text-warning mb-3"></i>
                <h4>4. Avalie</h4>
                <p class="text-muted">Partilhe a sua experiência</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">Pronto para começar?</h2>
        <p class="lead mb-4">Junte-se à maior comunidade canina em Portugal</p>
        <?php if (!isAuthenticated()): ?>
        <a href="register.php" class="btn btn-light btn-lg"><i class="bi bi-person-plus me-2"></i>Criar Conta</a>
        <?php else: ?>
        <a href="dashboard.php" class="btn btn-light btn-lg"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/resources/views/layouts/main.php';
?>