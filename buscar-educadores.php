<?php
require_once 'config/database.php';

$pageTitle = 'Buscar Educadores - AUnidos';

// Parâmetros de busca
$distrito = $_GET['distrito'] ?? '';
$especialidade = $_GET['especialidade'] ?? '';
$precoMin = $_GET['preco_min'] ?? '';
$precoMax = $_GET['preco_max'] ?? '';
$avaliacaoMin = $_GET['avaliacao_min'] ?? '';
$ordenacao = $_GET['ordenacao'] ?? 'avaliacao_desc';

// Construir query de busca
try {
    $db = getDB();
    
    $sql = "
        SELECT DISTINCT
            e.id,
            u.nome,
            u.distrito,
            u.telefone,
            e.anos_experiencia,
            e.biografia,
            e.preco_minimo,
            e.preco_maximo,
            e.avaliacao_media,
            e.total_avaliacoes,
            e.foto_perfil,
            GROUP_CONCAT(DISTINCT esp.nome ORDER BY esp.nome SEPARATOR ', ') as especialidades,
            COUNT(DISTINCT s.id) as total_servicos
        FROM educadores e
        JOIN utilizadores u ON e.utilizador_id = u.id
        LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
        LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
        LEFT JOIN servicos s ON e.id = s.educador_id AND s.ativo = TRUE
        WHERE e.aprovado = TRUE AND u.ativo = TRUE
    ";
    
    $params = [];
    
    // Filtro por distrito
    if (!empty($distrito)) {
        $sql .= " AND u.distrito = ?";
        $params[] = $distrito;
    }
    
    // Filtro por especialidade
    if (!empty($especialidade)) {
        $sql .= " AND esp.nome = ?";
        $params[] = $especialidade;
    }
    
    // Filtro por preço mínimo
    if (!empty($precoMin)) {
        $sql .= " AND e.preco_minimo >= ?";
        $params[] = $precoMin;
    }
    
    // Filtro por preço máximo
    if (!empty($precoMax)) {
        $sql .= " AND e.preco_maximo <= ?";
        $params[] = $precoMax;
    }
    
    // Filtro por avaliação mínima
    if (!empty($avaliacaoMin)) {
        $sql .= " AND e.avaliacao_media >= ?";
        $params[] = $avaliacaoMin;
    }
    
    $sql .= " GROUP BY e.id";
    
    // Ordenação
    switch ($ordenacao) {
        case 'avaliacao_desc':
            $sql .= " ORDER BY e.avaliacao_media DESC, e.total_avaliacoes DESC";
            break;
        case 'avaliacao_asc':
            $sql .= " ORDER BY e.avaliacao_media ASC";
            break;
        case 'preco_asc':
            $sql .= " ORDER BY e.preco_minimo ASC";
            break;
        case 'preco_desc':
            $sql .= " ORDER BY e.preco_minimo DESC";
            break;
        case 'experiencia_desc':
            $sql .= " ORDER BY e.anos_experiencia DESC";
            break;
        case 'nome_asc':
            $sql .= " ORDER BY u.nome ASC";
            break;
        default:
            $sql .= " ORDER BY e.avaliacao_media DESC, e.total_avaliacoes DESC";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $educadores = $stmt->fetchAll();
    
    // Obter especialidades para o filtro
    $stmt = $db->query("SELECT nome FROM especialidades ORDER BY nome");
    $todasEspecialidades = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    error_log("Erro na busca: " . $e->getMessage());
    $educadores = [];
    $todasEspecialidades = [];
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Cabeçalho -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold">
                <i class="bi bi-search me-3 text-primary"></i>Buscar Educadores
            </h1>
            <p class="lead text-muted">Encontre o educador canino ideal na sua região</p>
        </div>
    </div>
    
    <!-- Filtros de Busca -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filtros de Busca
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" id="searchForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="distrito" class="form-label">Distrito</label>
                        <select class="form-select" id="distrito" name="distrito">
                            <option value="">Todos os distritos</option>
                            <?php
                            $distritos = ['Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
                                         'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
                                         'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'];
                            foreach ($distritos as $d):
                            ?>
                                <option value="<?php echo $d; ?>" <?php echo ($distrito === $d) ? 'selected' : ''; ?>>
                                    <?php echo $d; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <select class="form-select" id="especialidade" name="especialidade">
                            <option value="">Todas as especialidades</option>                            
                            <?php 
                            $especialidade = ['Obediência Básica', 'Adestramento Avançado', 'Correção de Comportamento', 
                                              'Treino de Agilidade', 'Socialização de Filhotes', 'Treino para Exposições'];
                            foreach ($todasEspecialidades as $esp): ?>
                                <option value="<?php echo $esp; ?>" <?php echo ($especialidade === $esp) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($esp); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="preco_min" class="form-label">Preço Mín. (€)</label>
                        <input type="number" class="form-control" id="preco_min" name="preco_min" 
                               value="<?php echo htmlspecialchars($precoMin); ?>" min="0" step="5">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="preco_max" class="form-label">Preço Máx. (€)</label>
                        <input type="number" class="form-control" id="preco_max" name="preco_max" 
                               value="<?php echo htmlspecialchars($precoMax); ?>" min="0" step="5">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="avaliacao_min" class="form-label">Avaliação Mín.</label>
                        <select class="form-select" id="avaliacao_min" name="avaliacao_min">
                            <option value="">Qualquer</option>
                            <option value="3" <?php echo ($avaliacaoMin === '3') ? 'selected' : ''; ?>>3+ ⭐</option>
                            <option value="4" <?php echo ($avaliacaoMin === '4') ? 'selected' : ''; ?>>4+ ⭐</option>
                            <option value="4.5" <?php echo ($avaliacaoMin === '4.5') ? 'selected' : ''; ?>>4.5+ ⭐</option>
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="ordenacao" class="form-label">Ordenar por</label>
                        <select class="form-select" id="ordenacao" name="ordenacao">
                            <option value="avaliacao_desc" <?php echo ($ordenacao === 'avaliacao_desc') ? 'selected' : ''; ?>>
                                Melhor Avaliação
                            </option>
                            <option value="preco_asc" <?php echo ($ordenacao === 'preco_asc') ? 'selected' : ''; ?>>
                                Menor Preço
                            </option>
                            <option value="preco_desc" <?php echo ($ordenacao === 'preco_desc') ? 'selected' : ''; ?>>
                                Maior Preço
                            </option>
                            <option value="experiencia_desc" <?php echo ($ordenacao === 'experiencia_desc') ? 'selected' : ''; ?>>
                                Mais Experiência
                            </option>
                            <option value="nome_asc" <?php echo ($ordenacao === 'nome_asc') ? 'selected' : ''; ?>>
                                Nome A-Z
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-8 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-2"></i>Buscar
                        </button>
                        <a href="buscar-educadores.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Limpar Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Resultados -->
    <div class="row">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <?php echo count($educadores); ?> educador(es) encontrado(s)
                </h5>
            </div>
            
            <?php if (empty($educadores)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">Nenhum educador encontrado</h4>
                    <p class="text-muted">Tente ajustar os filtros de busca para ver mais resultados.</p>
                    <a href="buscar-educadores.php" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Ver Todos os Educadores
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($educadores as $educador): ?>
                    <div class="card mb-4 educador-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <img src="<?php echo $educador['foto_perfil'] ? UPLOAD_URL . 'perfis/' . $educador['foto_perfil'] : 'https://via.placeholder.com/150'; ?>" 
                                         alt="<?php echo htmlspecialchars($educador['nome']); ?>" 
                                         class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                    
                                    <div class="rating mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $educador['avaliacao_media']): ?>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star text-muted"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <div class="small text-muted">
                                            <?php echo number_format($educador['avaliacao_media'], 1); ?> 
                                            (<?php echo $educador['total_avaliacoes']; ?> avaliações)
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="card-title fw-bold">
                                        <?php echo htmlspecialchars($educador['nome']); ?>
                                    </h5>
                                    
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($educador['distrito']); ?>
                                        <span class="ms-3">
                                            <i class="bi bi-calendar me-1"></i><?php echo $educador['anos_experiencia']; ?> anos de experiência
                                        </span>
                                    </p>
                                    
                                    <?php if ($educador['especialidades']): ?>
                                    <div class="mb-2">
                                        <?php foreach (explode(', ', $educador['especialidades']) as $esp): ?>
                                            <span class="badge bg-primary me-1"><?php echo htmlspecialchars($esp); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <p class="card-text">
                                        <?php echo htmlspecialchars(substr($educador['biografia'], 0, 150)) . '...'; ?>
                                    </p>
                                    
                                    <div class="small text-muted">
                                        <i class="bi bi-gear me-1"></i><?php echo $educador['total_servicos']; ?> serviços disponíveis
                                    </div>
                                </div>
                                
                                <div class="col-md-3 text-end">
                                    <div class="price-range mb-3">
                                        <div class="h5 text-primary">
                                            €<?php echo number_format($educador['preco_minimo'], 0); ?> - €<?php echo number_format($educador['preco_maximo'], 0); ?>
                                        </div>
                                        <small class="text-muted">por sessão</small>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="educador.php?id=<?php echo $educador['id']; ?>" class="btn btn-primary">
                                            <i class="bi bi-eye me-2"></i>Ver Perfil
                                        </a>
                                        <a href="tel:<?php echo $educador['telefone']; ?>" class="btn btn-outline-success">
                                            <i class="bi bi-telephone me-2"></i>Contactar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Dicas de Busca
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use os filtros para encontrar educadores na sua área
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Verifique as avaliações e comentários
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Compare preços e especialidades
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Entre em contacto para mais informações
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-question-circle me-2"></i>Precisa de Ajuda?
                    </h6>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Não encontra o que procura? A nossa equipa está aqui para ajudar.
                    </p>
                    <a href="contacto.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope me-2"></i>Contactar Suporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-submit do formulário quando os filtros mudam
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const selects = form.querySelectorAll('select, input');
    
    selects.forEach(element => {
        element.addEventListener('change', function() {
            // Dar um pequeno delay para melhor UX
            setTimeout(() => {
                form.submit();
            }, 100);
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>