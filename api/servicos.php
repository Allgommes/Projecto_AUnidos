<?php
/**
 * API: CRUD de Serviços
 * 
 * GET    /api/servicos.php           - Listar todos os serviços
 * GET    /api/servicos.php?id=1      - Ver um serviço específico
 * POST   /api/servicos.php           - Criar novo serviço
 * PUT    /api/servicos.php?id=1      - Atualizar serviço
 * DELETE /api/servicos.php?id=1      - Deletar serviço
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
                // Buscar um serviço específico
                $stmt = $db->prepare("
                    SELECT s.*, u.nome as educador_nome, e.avaliacao_media
                    FROM servicos s
                    JOIN educadores e ON s.educador_id = e.id
                    JOIN utilizadores u ON e.utilizador_id = u.id
                    WHERE s.id = ?
                ");
                $stmt->execute([$id]);
                $servico = $stmt->fetch();
                
                if ($servico) {
                    echo json_encode([
                        'success' => true,
                        'data' => $servico
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Serviço não encontrado']);
                }
            } else {
                // Listar todos os serviços
                $filters = ['s.ativo = 1'];
                $params = [];
                
                if (isset($_GET['educador_id'])) {
                    $filters[] = "s.educador_id = ?";
                    $params[] = $_GET['educador_id'];
                }
                
                $whereClause = 'WHERE ' . implode(' AND ', $filters);
                
                $stmt = $db->prepare("
                    SELECT s.*, u.nome as educador_nome, u.distrito, e.avaliacao_media
                    FROM servicos s
                    JOIN educadores e ON s.educador_id = e.id
                    JOIN utilizadores u ON e.utilizador_id = u.id
                    $whereClause
                    ORDER BY s.data_criacao DESC
                ");
                $stmt->execute($params);
                $servicos = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $servicos,
                    'total' => count($servicos)
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            // Criar novo serviço
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required = ['educador_id', 'nome', 'preco'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => "Campo obrigatório: $field"]);
                    exit;
                }
            }
            
            // Verificar se educador existe
            $stmt = $db->prepare("SELECT id FROM educadores WHERE id = ?");
            $stmt->execute([$data['educador_id']]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Educador não encontrado']);
                break;
            }
            
            // Criar serviço
            $stmt = $db->prepare("
                INSERT INTO servicos (educador_id, nome, descricao, preco, duracao_minutos, ativo)
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $data['educador_id'],
                $data['nome'],
                $data['descricao'] ?? null,
                $data['preco'],
                $data['duracao_minutos'] ?? 60
            ]);
            
            $servicoId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Serviço criado com sucesso',
                'data' => ['id' => (int)$servicoId]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            // Atualizar serviço
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            $allowedFields = ['nome', 'descricao', 'preco', 'duracao_minutos', 'ativo'];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Nenhum campo para atualizar']);
                break;
            }
            
            $params[] = $id;
            $sql = "UPDATE servicos SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode([
                'success' => true,
                'message' => 'Serviço atualizado com sucesso'
            ]);
            break;
            
        case 'DELETE':
            // Soft delete - apenas desativar
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $stmt = $db->prepare("UPDATE servicos SET ativo = 0 WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Serviço desativado com sucesso'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Serviço não encontrado']);
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
