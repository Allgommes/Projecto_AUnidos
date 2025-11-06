<?php
require_once __DIR__ . '/bootstrap.php';

// Verificar se está logado
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$pageTitle = 'Dashboard - AUnidos';
$userType = getUserType();
$userId = $_SESSION['user_id'];

try {
    $db = getDB();
    
    if ($userType === 'educador') {
        // Dados para educador
        $stmt = $db->prepare("
            SELECT e.*, u.nome, u.email, u.distrito, u.telefone
            FROM educadores e 
            JOIN utilizadores u ON e.utilizador_id = u.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $perfil = $stmt->fetch();
        
        // Estatísticas do educador
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_servicos
            FROM servicos 
            WHERE educador_id = ? AND ativo = TRUE
        ");
        $stmt->execute([$perfil['id']]);
        $stats['servicos'] = $stmt->fetch()['total_servicos'];
        
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_agendamentos,
                COUNT(CASE WHEN status = 'pendente' THEN 1 END) as pendentes,
                COUNT(CASE WHEN status = 'confirmado' THEN 1 END) as confirmados
            FROM agendamentos 
            WHERE educador_id = ?
        ");
        $stmt->execute([$perfil['id']]);
        $agendamentosStats = $stmt->fetch();
        $stats['agendamentos'] = $agendamentosStats['total_agendamentos'];
        $stats['pendentes'] = $agendamentosStats['pendentes'];
        $stats['confirmados'] = $agendamentosStats['confirmados'];
        
        // Agendamentos recentes
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.data_agendamento,
                a.status,
                a.preco,
                s.nome as servico_nome,
                u.nome as cliente_nome,
                u.telefone as cliente_telefone
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            JOIN donos d ON a.dono_id = d.id
            JOIN utilizadores u ON d.utilizador_id = u.id
            WHERE a.educador_id = ?
            ORDER BY a.data_agendamento DESC
            LIMIT 5
        ");
        $stmt->execute([$perfil['id']]);
        $agendamentos = $stmt->fetchAll();
        
        // Avaliações recentes
        $stmt = $db->prepare("
            SELECT 
                av.nota,
                av.comentario,
                av.data_criacao,
                u.nome as cliente_nome
            FROM avaliacoes av
            JOIN donos d ON av.dono_id = d.id
            JOIN utilizadores u ON d.utilizador_id = u.id
            WHERE av.educador_id = ?
            ORDER BY av.data_criacao DESC
            LIMIT 3
        ");
        $stmt->execute([$perfil['id']]);
        $avaliacoes = $stmt->fetchAll();
        
    } else {
        // Dados para dono
        $stmt = $db->prepare("
            SELECT d.*, u.nome, u.email, u.distrito, u.telefone
            FROM donos d 
            JOIN utilizadores u ON d.utilizador_id = u.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $perfil = $stmt->fetch();
        
        // Agendamentos do dono
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.data_agendamento,
                a.status,
                a.preco,
                s.nome as servico_nome,
                u.nome as educador_nome,
                u.telefone as educador_telefone,
                e.id as educador_id
            FROM agendamentos a
            JOIN servicos s ON a.servico_id = s.id
            JOIN educadores e ON a.educador_id = e.id
            JOIN utilizadores u ON e.utilizador_id = u.id
            WHERE a.dono_id = ?
            ORDER BY a.data_agendamento DESC
            LIMIT 5
        ");
        $stmt->execute([$perfil['id']]);
        $agendamentos = $stmt->fetchAll();
        
        // Estatísticas do dono
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_agendamentos,
                COUNT(CASE WHEN status = 'concluido' THEN 1 END) as concluidos
            FROM agendamentos 
            WHERE dono_id = ?
        ");
        $stmt->execute([$perfil['id']]);
        $agendamentosStats = $stmt->fetch();
        $stats['agendamentos'] = $agendamentosStats['total_agendamentos'];
        $stats['concluidos'] = $agendamentosStats['concluidos'];
        
        // Cães do dono
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_caes
            FROM caes 
            WHERE dono_id = ? AND ativo = TRUE
        ");
        $stmt->execute([$perfil['id']]);
        $stats['caes'] = $stmt->fetch()['total_caes'];
    }
    
} catch (Exception $e) {
    error_log("Erro no dashboard: " . $e->getMessage());
    $perfil = null;
    $stats = [];
    $agendamentos = [];
    $avaliacoes = [];
}

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- Header do Dashboard -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6 fw-bold">
                <i class="bi bi-speedometer2 me-3 text-primary"></i>Dashboard
            </h1>
            <p class="lead text-muted">
                Bem-vindo de volta, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
            </p>
        </div>
        <div class="col-auto">
            <a href="perfil.php" class="btn btn-outline-primary">
                <i class="bi bi-person me-2"></i>Editar Perfil
            </a>
        </div>
    </div>
    
    <!-- Aviso de email não verificado -->
    <?php if (!$_SESSION['email_verified']): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Email não verificado!</strong> 
        Verifique a sua caixa de email e clique no link de verificação para ativar todas as funcionalidades.
    </div>
    <?php endif; ?>
    
    <!-- Estatísticas -->
    <div class="row mb-4">
        <?php if ($userType === 'educador'): ?>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-gear display-4 text-primary mb-2"></i>
                    <div class="stats-number"><?php echo $stats['servicos'] ?? 0; ?></div>
                    <h6>Serviços Ativos</h6>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-calendar-check display-4 text-success mb-2"></i>
                    <div class="stats-number"><?php echo $stats['agendamentos'] ?? 0; ?></div>
                    <h6>Total Agendamentos</h6>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-clock display-4 text-warning mb-2"></i>
                    <div class="stats-number"><?php echo $stats['pendentes'] ?? 0; ?></div>
                    <h6>Pendentes</h6>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-star-fill display-4 text-info mb-2"></i>
                    <div class="stats-number"><?php echo number_format($perfil['avaliacao_media'] ?? 0, 1); ?></div>
                    <h6>Avaliação Média</h6>
                </div>
            </div>
        <?php else: ?>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-calendar-check display-4 text-primary mb-2"></i>
                    <div class="stats-number"><?php echo $stats['agendamentos'] ?? 0; ?></div>
                    <h6>Total Agendamentos</h6>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                    <div class="stats-number"><?php echo $stats['concluidos'] ?? 0; ?></div>
                    <h6>Concluídos</h6>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <i class="bi bi-heart-fill display-4 text-danger mb-2"></i>
                    <div class="stats-number"><?php echo $stats['caes'] ?? 0; ?></div>
                    <h6>Cães Registados</h6>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Agendamentos Recentes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar me-2"></i>Agendamentos Recentes
                    </h5>
                    <?php if ($userType === 'educador'): ?>
                    <a href="agendamentos.php" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($agendamentos)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-4 text-muted"></i>
                            <p class="mt-3 text-muted">Nenhum agendamento encontrado.</p>
                            <?php if ($userType === 'dono'): ?>
                            <a href="buscar-educadores.php" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Buscar Educadores
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th><?php echo $userType === 'educador' ? 'Cliente' : 'Educador'; ?></th>
                                        <th>Serviço</th>
                                        <th>Status</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agendamentos as $agendamento): ?>
                                    <tr>
                                        <td>
                                            <small>
                                                <?php echo date('d/m/Y H:i', strtotime($agendamento['data_agendamento'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($userType === 'educador'): ?>
                                                <?php echo htmlspecialchars($agendamento['cliente_nome']); ?>
                                                <br><small class="text-muted"><?php echo $agendamento['cliente_telefone']; ?></small>
                                            <?php else: ?>
                                                <a href="educador.php?id=<?php echo $agendamento['educador_id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($agendamento['educador_nome']); ?>
                                                </a>
                                                <br><small class="text-muted"><?php echo $agendamento['educador_telefone']; ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($agendamento['servico_nome']); ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pendente' => 'warning',
                                                'confirmado' => 'info',
                                                'em_andamento' => 'primary',
                                                'concluido' => 'success',
                                                'cancelado' => 'danger'
                                            ];
                                            $statusText = [
                                                'pendente' => 'Pendente',
                                                'confirmado' => 'Confirmado',
                                                'em_andamento' => 'Em Andamento',
                                                'concluido' => 'Concluído',
                                                'cancelado' => 'Cancelado'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass[$agendamento['status']] ?? 'secondary'; ?>">
                                                <?php echo $statusText[$agendamento['status']] ?? $agendamento['status']; ?>
                                            </span>
                                        </td>
                                        <td>€<?php echo number_format($agendamento['preco'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($userType === 'educador'): ?>
                            <a href="meus-servicos.php" class="btn btn-primary">
                                <i class="bi bi-gear me-2"></i>Gerir Serviços
                            </a>
                            <a href="agendamentos.php" class="btn btn-outline-primary">
                                <i class="bi bi-calendar me-2"></i>Ver Agendamentos
                            </a>
                            <?php if ($perfil && !$perfil['aprovado']): ?>
                            <div class="alert alert-warning p-2 mb-0">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Perfil em análise
                                </small>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="buscar-educadores.php" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Buscar Educadores
                            </a>
                            <a href="meus-caes.php" class="btn btn-outline-primary">
                                <i class="bi bi-heart me-2"></i>Meus Cães
                            </a>
                            <a href="agendamentos.php" class="btn btn-outline-secondary">
                                <i class="bi bi-calendar me-2"></i>Meus Agendamentos
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Perfil Resumo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person me-2"></i>Meu Perfil
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="<?php echo $perfil['foto_perfil'] ? UPLOAD_URL . 'perfis/' . $perfil['foto_perfil'] : 'https://via.placeholder.com/80'; ?>" 
                             alt="Foto de perfil" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    <h6 class="text-center"><?php echo htmlspecialchars($perfil['nome']); ?></h6>
                    <p class="text-center text-muted small">
                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($perfil['distrito']); ?>
                    </p>
                    
                    <?php if ($userType === 'educador'): ?>
                    <div class="text-center">
                        <div class="small text-muted">
                            <?php echo $perfil['anos_experiencia']; ?> anos de experiência
                        </div>
                        <div class="rating mt-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= ($perfil['avaliacao_media'] ?? 0)): ?>
                                    <i class="bi bi-star-fill text-warning"></i>
                                <?php else: ?>
                                    <i class="bi bi-star text-muted"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <small class="ms-1 text-muted">
                                (<?php echo $perfil['total_avaliacoes'] ?? 0; ?>)
                            </small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-grid mt-3">
                        <a href="perfil.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-2"></i>Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Avaliações Recentes (apenas para educadores) -->
            <?php if ($userType === 'educador' && !empty($avaliacoes)): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-star me-2"></i>Avaliações Recentes
                    </h6>
                </div>
                <div class="card-body">
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong class="small"><?php echo htmlspecialchars($avaliacao['cliente_nome']); ?></strong>
                            <div class="rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $avaliacao['nota']): ?>
                                        <i class="bi bi-star-fill text-warning small"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star text-muted small"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="small mb-1"><?php echo htmlspecialchars(substr($avaliacao['comentario'], 0, 80)) . '...'; ?></p>
                        <small class="text-muted">
                            <?php echo date('d/m/Y', strtotime($avaliacao['data_criacao'])); ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>