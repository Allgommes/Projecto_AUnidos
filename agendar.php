<?php
// agendar.php
require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'dono') {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

// obter dono_id
$stmt = $pdo->prepare("SELECT id FROM donos WHERE utilizador_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$dono = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dono) {
    echo json_encode(['error' => 'Perfil de dono não encontrado']);
    exit;
}

$data = $_POST;
$dono_id = $dono['id'];
$educador_id = $data['educador_id'];
$servico_id = $data['servico_id'];
$data_agendamento = $data['data_agendamento']; // 'YYYY-MM-DD HH:MM:SS'
$preco = $data['preco'];

$stmt = $pdo->prepare("INSERT INTO agendamentos (dono_id, educador_id, servico_id, data_agendamento, duracao_minutos, status, preco, notas) VALUES (?, ?, ?, ?, ?, 'pendente', ?, ?)");
$stmt->execute([$dono_id, $educador_id, $servico_id, $data_agendamento, $data['duracao_minutos'] ?? 60, $preco, $data['notas'] ?? null]);

$agendamento_id = $pdo->lastInsertId();

// criar notificação para o educador
// obter utilizador_id do educador
$stmt = $pdo->prepare("SELECT utilizador_id FROM educadores WHERE id = ?");
$stmt->execute([$educador_id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
if ($u) {
    $titulo = "Novo pedido de agendamento";
    $conteudo = "Tem um novo pedido de agendamento (ID $agendamento_id).";
    $stmt = $pdo->prepare("INSERT INTO notificacoes (utilizador_id, tipo, titulo, conteudo) VALUES (?, 'novo_agendamento', ?, ?)");
    $stmt->execute([$u['utilizador_id'], $titulo, $conteudo]);
    // opcional: enviar email ao educador (PHPMailer)
}

echo json_encode(['success' => true, 'message' => 'Agendamento criado', 'agendamento_id' => $agendamento_id]);
exit;
?>