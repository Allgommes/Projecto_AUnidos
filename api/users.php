<?php
/**
 * API: CRUD de Utilizadores
 * 
 * GET    /api/users.php           - Listar todos os utilizadores
 * GET    /api/users.php?id=1      - Ver um utilizador específico
 * POST   /api/users.php           - Criar novo utilizador
 * PUT    /api/users.php?id=1      - Atualizar utilizador
 * DELETE /api/users.php?id=1      - Deletar utilizador
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                // Buscar um utilizador específico
                $stmt = $db->prepare("
                    SELECT u.*, 
                           CASE 
                               WHEN e.id IS NOT NULL THEN 'educador'
                               WHEN d.id IS NOT NULL THEN 'dono'
                               ELSE u.tipo_utilizador
                           END as tipo_real
                    FROM utilizadores u
                    LEFT JOIN educadores e ON u.id = e.utilizador_id
                    LEFT JOIN donos d ON u.id = d.utilizador_id
                    WHERE u.id = ?
                ");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
                
                if ($user) {
                    unset($user['password_hash']); // Não retornar password
                    echo json_encode([
                        'success' => true,
                        'data' => $user
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Utilizador não encontrado'
                    ]);
                }
            } else {
                // Listar todos os utilizadores
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
                
                $stmt = $db->prepare("
                    SELECT u.id, u.nome, u.email, u.tipo_utilizador, u.distrito, u.ativo, 
                           u.email_verificado, u.data_criacao
                    FROM utilizadores u
                    ORDER BY u.data_criacao DESC
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$limit, $offset]);
                $users = $stmt->fetchAll();
                
                $stmtTotal = $db->query("SELECT COUNT(*) as total FROM utilizadores");
                $total = $stmtTotal->fetch()['total'];
                
                echo json_encode([
                    'success' => true,
                    'data' => $users,
                    'pagination' => [
                        'total' => (int)$total,
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            // Criar novo utilizador
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['nome'], $data['email'], $data['password'], $data['tipo_utilizador'], $data['distrito'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Campos obrigatórios: nome, email, password, tipo_utilizador, distrito'
                ]);
                break;
            }
            
            // Verificar se email já existe
            $stmt = $db->prepare("SELECT id FROM utilizadores WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'error' => 'Email já está cadastrado'
                ]);
                break;
            }
            
            // Criar utilizador
            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt = $db->prepare("
                INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, ativo)
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $data['nome'],
                $data['email'],
                $passwordHash,
                $data['tipo_utilizador'],
                $data['telefone'] ?? null,
                $data['distrito']
            ]);
            
            $userId = $db->lastInsertId();
            
            // Criar registro de dono ou educador
            if ($data['tipo_utilizador'] === 'dono') {
                $stmt = $db->prepare("INSERT INTO donos (utilizador_id) VALUES (?)");
                $stmt->execute([$userId]);
            } elseif ($data['tipo_utilizador'] === 'educador') {
                $stmt = $db->prepare("INSERT INTO educadores (utilizador_id) VALUES (?)");
                $stmt->execute([$userId]);
            }
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Utilizador criado com sucesso',
                'data' => [
                    'id' => (int)$userId,
                    'nome' => $data['nome'],
                    'email' => $data['email']
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            // Atualizar utilizador
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            if (isset($data['nome'])) {
                $updates[] = "nome = ?";
                $params[] = $data['nome'];
            }
            if (isset($data['telefone'])) {
                $updates[] = "telefone = ?";
                $params[] = $data['telefone'];
            }
            if (isset($data['distrito'])) {
                $updates[] = "distrito = ?";
                $params[] = $data['distrito'];
            }
            if (isset($data['ativo'])) {
                $updates[] = "ativo = ?";
                $params[] = $data['ativo'] ? 1 : 0;
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Nenhum campo para atualizar']);
                break;
            }
            
            $params[] = $id;
            $sql = "UPDATE utilizadores SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode([
                'success' => true,
                'message' => 'Utilizador atualizado com sucesso'
            ]);
            break;
            
        case 'DELETE':
            // Deletar utilizador
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $stmt = $db->prepare("DELETE FROM utilizadores WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Utilizador deletado com sucesso'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Utilizador não encontrado'
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
