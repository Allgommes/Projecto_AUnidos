<?php
// register.php
require_once __DIR__ . '/bootstrap.php';

// GET: renderizar formulário de registo
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pageTitle = 'Registar - AUnidos';
    // Render view dentro do layout principal
    ob_start();
    require __DIR__ . '/resources/views/auth/register.php';
    $content = ob_get_clean();
    require __DIR__ . '/resources/views/layouts/main.php';
    exit;
}

// POST: processar registo (aceita form-urlencoded ou JSON)
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

// Sanitização/validação segura
$nome  = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$tipo = $data['tipo_utilizador'] ?? '';
$telefone = $data['telefone'] ?? null;
$distrito = $data['distrito'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $nome === '' || $password === '' || !in_array($tipo, ['dono', 'educador'], true)) {
    if ($isJson) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
    } else {
        setOld($data);
        setFlash('error', 'Por favor, verifique os dados do formulário.');
        redirect('register.php');
    }
    exit;
}

try {
    $pdo = getDB();
    $pdo->beginTransaction();

    // Verificar email duplicado
    $chk = $pdo->prepare('SELECT id FROM utilizadores WHERE email = ?');
    $chk->execute([$email]);
    if ($chk->fetch()) {
        if ($isJson) {
            http_response_code(409);
            echo json_encode(['error' => 'Email já registado']);
        } else {
            setOld($data);
            setFlash('error', 'Este email já está registado.');
            redirect('register.php');
        }
        exit;
    }

    // Inserir utilizador
    $stmt = $pdo->prepare('INSERT INTO utilizadores (nome,email,password_hash,tipo_utilizador,telefone,distrito,token_verificacao,ativo,email_verificado) VALUES (?,?,?,?,?,?,?,1,0)');
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $token_ver = bin2hex(random_bytes(16));
    $stmt->execute([$nome, $email, $password_hash, $tipo, $telefone, $distrito, $token_ver]);
    $utilizador_id = (int)$pdo->lastInsertId();

    if ($tipo === 'dono') {
        // Tabela donos só tem (id, utilizador_id)
        $stmt = $pdo->prepare('INSERT INTO donos (utilizador_id) VALUES (?)');
        $stmt->execute([$utilizador_id]);
    } else {
        // Inserir educador com colunas existentes no schema
        $stmt = $pdo->prepare('INSERT INTO educadores (utilizador_id, anos_experiencia, biografia, certificacoes, foto_perfil, aprovado) VALUES (?, ?, ?, ?, ?, 0)');
        $stmt->execute([
            $utilizador_id,
            $data['anos_experiencia'] ?? 0,
            $data['biografia'] ?? null,
            $data['certificacoes'] ?? null,
            $data['foto_perfil'] ?? null,
        ]);
    }

    $pdo->commit();
    
    // Enviar email de verificação
    try {
        require_once __DIR__ . '/app/Services/EmailService.php';
        $emailService = new App\Services\EmailService();
        $emailService->sendVerificationEmail($email, $nome, $token_ver);
    } catch (Exception $emailError) {
        error_log('Erro ao enviar email de verificação: ' . $emailError->getMessage());
        // Não bloquear o registo se email falhar
    }
    
    if ($isJson) {
        echo json_encode(['success' => true, 'message' => 'Registo efetuado. Verifique o seu email.', 'data' => ['id' => $utilizador_id]]);
    } else {
        clearOld();
        setFlash('success', 'Conta criada com sucesso! Verifique o seu email para ativar a conta.');
        redirect('login.php');
    }
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if ($isJson) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        setOld($data ?? []);
        setFlash('error', 'Ocorreu um erro ao registar. Tente novamente.');
        redirect('register.php');
    }
}
exit;
?>