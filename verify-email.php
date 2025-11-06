<?php
require_once __DIR__ . '/bootstrap.php';

$pageTitle = 'Verificar Email - AUnidos';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    setFlash('error', 'Token de verificação inválido.');
    redirect('login.php');
    exit;
}

try {
    $db = getDB();
    
    // Buscar utilizador pelo token
    $stmt = $db->prepare('
        SELECT id, nome, email, email_verificado 
        FROM utilizadores 
        WHERE token_verificacao = ? AND ativo = 1
    ');
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        setFlash('error', 'Token inválido ou expirado.');
        redirect('login.php');
        exit;
    }
    
    if ($user['email_verificado']) {
        setFlash('success', 'Email já foi verificado anteriormente!');
        redirect('login.php');
        exit;
    }
    
    // Marcar email como verificado
    $stmt = $db->prepare('
        UPDATE utilizadores 
        SET email_verificado = 1, token_verificacao = NULL 
        WHERE id = ?
    ');
    $stmt->execute([$user['id']]);
    
    setFlash('success', 'Email verificado com sucesso! Já pode fazer login.');
    redirect('login.php');
    
} catch (Exception $e) {
    error_log('Erro ao verificar email: ' . $e->getMessage());
    setFlash('error', 'Ocorreu um erro ao verificar o email.');
    redirect('login.php');
}
?>
