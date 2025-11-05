<?php
// register.php
require_once 'config/database.php';
header('Content-Type: application/json');

$data = $_POST; // ou json_decode(file_get_contents('php://input'), true);

// validações básicas
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || empty($data['nome']) || empty($data['password']) || !in_array($data['tipo_utilizador'], ['dono', 'educador'])) {
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // inserir utilizador
    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome,email,password_hash,tipo_utilizador,telefone,distrito,token_verificacao) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $token_ver = bin2hex(random_bytes(16));
    $stmt->execute([$data['nome'], $data['email'], $password_hash, $data['tipo_utilizador'], $data['telefone'] ?? null, $data['distrito'] ?? null, $token_ver]);
    $utilizador_id = $pdo->lastInsertId();

    if ($data['tipo_utilizador'] === 'dono') {
        $stmt = $pdo->prepare("INSERT INTO donos (utilizador_id, foto_perfil) VALUES (?, ?)");
        $stmt->execute([$utilizador_id, $data['foto_perfil'] ?? null]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO educadores (utilizador_id, anos_experiencia, biografia, certificacoes, preco_minimo, preco_maximo, disponibilidade, foto_perfil, aprovado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$utilizador_id, $data['anos_experiencia'] ?? 0, $data['biografia'] ?? null, $data['certificacoes'] ?? null, $data['preco_minimo'] ?? null, $data['preco_maximo'] ?? null, $data['disponibilidade'] ?? null, $data['foto_perfil'] ?? null]);
    }

    // enviar email de verificação (pode usar PHPMailer) — deixar stub por agora
    // enviar_email_verificacao($data['email'], $token_ver);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Registo efetuado. Verifique o seu email.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
exit;
?>