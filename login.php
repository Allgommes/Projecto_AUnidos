<?php
// login.php
require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT id, nome, email, password_hash, tipo_utilizador, ativo FROM utilizadores WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'Credenciais inválidas']);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(['error' => 'Credenciais inválidas']);
    exit;
}

if (!$user['ativo']) {
    echo json_encode(['error' => 'Conta inativa']);
    exit;
}

// criação de sessão simples
$_SESSION['user_id'] = $user['id'];
$_SESSION['nome'] = $user['nome'];
$_SESSION['tipo'] = $user['tipo_utilizador'];

echo json_encode(['success' => true, 'message' => 'Login efetuado']);
exit;
?>