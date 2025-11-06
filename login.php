<?php
// login.php
require_once __DIR__ . '/bootstrap.php';

// GET -> renderizar formulário
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pageTitle = 'Login - AUnidos';
    ob_start();
    require __DIR__ . '/resources/views/auth/login.php';
    $content = ob_get_clean();
    require __DIR__ . '/resources/views/layouts/main.php';
    exit;
}

// POST -> processar login (form ou JSON)
$isJson = false;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    header('Content-Type: application/json; charset=UTF-8');
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    $data = is_array($json) ? $json : [];
    $isJson = true;
} else {
    $data = $_POST;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    if ($isJson) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
    } else {
        setFlash('error', 'Por favor, verifique o email e a password.');
        redirect('login.php');
    }
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT id, nome, email, password_hash, tipo_utilizador, ativo, email_verificado FROM utilizadores WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        if ($isJson) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciais inválidas']);
        } else {
            setFlash('error', 'Credenciais inválidas');
            redirect('login.php');
        }
        exit;
    }

    if (!$user['ativo']) {
        if ($isJson) {
            http_response_code(403);
            echo json_encode(['error' => 'Conta inativa']);
        } else {
            setFlash('error', 'Conta inativa.');
            redirect('login.php');
        }
        exit;
    }

    // criar sessão
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['nome'];
    $_SESSION['user_type'] = $user['tipo_utilizador'];
    $_SESSION['email_verified'] = (bool)$user['email_verificado'];

    if ($isJson) {
        echo json_encode(['success' => true, 'message' => 'Login efetuado']);
    } else {
        setFlash('success', 'Bem-vindo, ' . e($user['nome']) . '!');
        redirect('dashboard.php');
    }
} catch (Exception $e) {
    if ($isJson) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        setFlash('error', 'Ocorreu um erro ao autenticar.');
        redirect('login.php');
    }
}
exit;
?>