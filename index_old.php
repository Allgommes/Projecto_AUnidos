<?php
require_once 'config/database.php';

$pageTitle = 'AUnidos - Conectando Donos e Educadores Caninos';

// Obter estatísticas para mostrar na home
try {
    $db = getDB();
    
    // Contar educadores ativos
    $stmt = $db->query("
        SELECT COUNT(*) as total_educadores 
        FROM educadores e 
        JOIN utilizadores u ON e.utilizador_id = u.id 
        WHERE e.aprovado = TRUE AND u.ativo = TRUE
    ");
    $totalEducadores = $stmt->fetch()['total_educadores'];
    
    // Contar donos ativos
    $stmt = $db->query("
        SELECT COUNT(*) as total_donos 
        FROM donos d 
        JOIN utilizadores u ON d.utilizador_id = u.id 
        WHERE u.ativo = TRUE
    ");
    $totalDonos = $stmt->fetch()['total_donos'];
    
    // Contar serviços disponíveis
    $stmt = $db->query("
        SELECT COUNT(*) as total_servicos 
        FROM servicos s 
        JOIN educadores e ON s.educador_id = e.id 
        WHERE s.ativo = TRUE AND e.aprovado = TRUE
    ");
    $totalServicos = $stmt->fetch()['total_servicos'];
    
    // Obter educadores em destaque (com melhor avaliação)
    $stmt = $db->query("
        SELECT u.nome, e.avaliacao_media, e.total_avaliacoes, u.distrito, e.foto_perfil,
               GROUP_CONCAT(esp.nome SEPARATOR ', ') as especialidades, e.id
        FROM educadores e
        JOIN utilizadores u ON e.utilizador_id = u.id
        LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
        LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
        WHERE e.aprovado = TRUE AND u.ativo = TRUE AND e.total_avaliacoes > 0
        GROUP BY e.id
        ORDER BY e.avaliacao_media DESC, e.total_avaliacoes DESC
        LIMIT 6
    ");
    $educadoresDestaque = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao obter estatísticas: " . $e->getMessage());
    $totalEducadores = 0;
    $totalDonos = 0;
    $totalServicos = 0;
    $educadoresDestaque = [];
}

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Conectamos <span class="text-warning">donos</span> e <span class="text-warning">educadores</span> caninos
                    </h1>
                    <p class="lead mb-4">
                        Encontre o educador ideal para o seu cão ou ofereça os seus serviços de treino na plataforma líder em Portugal.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="buscar-educadores.php" class="btn btn-light btn-lg">
                            <i class="bi bi-search me-2"></i>Buscar Educadores
                        </a>
                        <?php if (!isLoggedIn()): ?>
                        <a href="register.php?type=educador" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-person-plus me-2"></i>Tornar-me Educador
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=500&h=400&fit=crop&crop=center" 
                         alt="Cão feliz com educador" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Box -->
<section class="container">
    <div class="search-box">
        <h3 class="text-center mb-4">
            <i class="bi bi-search me-2 text-primary"></i>Encontre o Educador Ideal
        </h3>
        <form action="buscar-educadores.php" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="distrito" class="form-label">Distrito</label>
                <select class="form-select" id="distrito" name="distrito">
                    <option value="">Todos os distritos</option>
                    <option value="Aveiro">Aveiro</option>
                    <option value="Beja">Beja</option>
                    <option value="Braga">Braga</option>
                    <option value="Bragança">Bragança</option>
                    <option value="Castelo Branco">Castelo Branco</option>
                    <option value="Coimbra">Coimbra</option>
                    <option value="Évora">Évora</option>
                    <option value="Faro">Faro</option>
                    <option value="Guarda">Guarda</option>
                    <option value="Leiria">Leiria</option>
                    <option value="Lisboa">Lisboa</option>
                    <option value="Portalegre">Portalegre</option>
                    <option value="Porto">Porto</option>
                    <option value="Santarém">Santarém</option>
                    <option value="Setúbal">Setúbal</option>
                    <option value="Viana do Castelo">Viana do Castelo</option>
                    <option value="Vila Real">Vila Real</option>
                    <option value="Viseu">Viseu</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="especialidade" class="form-label">Especialidade</label>
                <select class="form-select" id="especialidade" name="especialidade">
                    <option value="">Todas as especialidades</option>
                    <option value="Obediência">Obediência</option>
                    <option value="Agility">Agility</option>
                    <option value="Modificação de Comportamento">Modificação de Comportamento</option>
                    <option value="Treino de Cachorros">Treino de Cachorros</option>
                    <option value="Treino de Cães de Serviço">Treino de Cães de Serviço</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Pesquisar
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Estatísticas -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($totalEducadores); ?></div>
                    <h5 class="mt-2">Educadores Certificados</h5>
                    <p class="text-muted">Profissionais verificados e aprovados</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($totalDonos); ?></div>
                    <h5 class="mt-2">Donos Registados</h5>
                    <p class="text-muted">Comunidade ativa de tutores</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($totalServicos); ?></div>
                    <h5 class="mt-2">Serviços Disponíveis</h5>
                    <p class="text-muted">Variedade de treinos e especializações</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Educadores em Destaque -->
<?php if (!empty($educadoresDestaque)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                <i class="bi bi-star-fill text-warning me-2"></i>Educadores em Destaque
            </h2>
            <p class="lead text-muted">Os educadores mais bem avaliados pela nossa comunidade</p>
        </div>
        
        <div class="row">
            <?php foreach ($educadoresDestaque as $educador): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card educador-card h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo $educador['foto_perfil'] ? UPLOAD_URL . 'perfis/' . $educador['foto_perfil'] : 'https://via.placeholder.com/80'; ?>" 
                             alt="<?php echo htmlspecialchars($educador['nome']); ?>" class="avatar mb-3">
                        
                        <h5 class="card-title"><?php echo htmlspecialchars($educador['nome']); ?></h5>
                        
                        <div class="rating mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $educador['avaliacao_media']): ?>
                                    <i class="bi bi-star-fill"></i>
                                <?php else: ?>
                                    <i class="bi bi-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <span class="ms-1 text-muted">
                                (<?php echo $educador['total_avaliacoes']; ?> avaliações)
                            </span>
                        </div>
                        
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($educador['distrito']); ?>
                        </p>
                        
                        <?php if ($educador['especialidades']): ?>
                        <div class="mb-3">
                            <?php foreach (explode(', ', $educador['especialidades']) as $esp): ?>
                                <span class="badge bg-primary me-1"><?php echo htmlspecialchars($esp); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="educador.php?id=<?php echo $educador['id']; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Ver Perfil
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="buscar-educadores.php" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Ver Todos os Educadores
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Como Funciona -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Como Funciona</h2>
            <p class="lead text-muted">Simples, rápido e seguro</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-person-plus display-1 text-primary"></i>
                </div>
                <h5>1. Registe-se</h5>
                <p class="text-muted">Crie a sua conta gratuita como dono ou educador</p>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-search display-1 text-primary"></i>
                </div>
                <h5>2. Procure</h5>
                <p class="text-muted">Encontre educadores na sua área com as especialidades que procura</p>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-calendar-check display-1 text-primary"></i>
                </div>
                <h5>3. Contacte</h5>
                <p class="text-muted">Entre em contacto e escolha o melhor serviço para o seu cão</p>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-star-fill display-1 text-primary"></i>
                </div>
                <h5>4. Avalie</h5>
                <p class="text-muted">Partilhe a sua experiência para ajudar outros donos</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4">Pronto para Começar?</h2>
                <p class="lead mb-4">
                    Junte-se à maior comunidade de donos e educadores caninos em Portugal
                </p>
                <?php if (!isLoggedIn()): ?>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="register.php?type=dono" class="btn btn-light btn-lg">
                        <i class="bi bi-heart me-2"></i>Sou Dono de Cão
                    </a>
                    <a href="register.php?type=educador" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-award me-2"></i>Sou Educador
                    </a>
                </div>
                <?php else: ?>
                <a href="dashboard.php" class="btn btn-light btn-lg">
                    <i class="bi bi-speedometer2 me-2"></i>Ir para Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>