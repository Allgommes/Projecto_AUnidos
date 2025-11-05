<?php
// servico_create.php
require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] !== 'educador') {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

$educador_id = null;
// obter id do educador pela tabela educadores
$stmt = $pdo->prepare("SELECT id FROM educadores WHERE utilizador_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) $educador_id = $row['id'];
else {
    echo json_encode(['error' => 'Perfil de educador não encontrado']);
    exit;
}

$data = $_POST;
$stmt = $pdo->prepare("INSERT INTO servicos (educador_id, nome, descricao, preco, duracao_minutos, ativo) VALUES (?, ?, ?, ?, ?, 1)");
$stmt->execute([$educador_id, $data['nome'], $data['descricao'] ?? null, $data['preco'], $data['duracao_minutos'] ?? 60]);

echo json_encode(['success' => true, 'message' => 'Serviço criado']);
exit;
?>