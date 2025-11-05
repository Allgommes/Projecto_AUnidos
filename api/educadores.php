<?php
/**
 * API: CRUD de Educadores
 * 
 * GET    /api/educadores.php           - Listar todos os educadores
 * GET    /api/educadores.php?id=1      - Ver um educador específico
 * POST   /api/educadores.php           - Criar novo educador
 * PUT    /api/educadores.php?id=1      - Atualizar educador
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
                // Buscar um educador específico
                $stmt = $db->prepare("
                    SELECT e.*, u.nome, u.email, u.distrito, u.telefone,
                           GROUP_CONCAT(esp.nome SEPARATOR ', ') as especialidades
                    FROM educadores e
                    JOIN utilizadores u ON e.utilizador_id = u.id
                    LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
                    LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
                    WHERE e.id = ?
                    GROUP BY e.id
                ");
                $stmt->execute([$id]);
                $educador = $stmt->fetch();
                
                if ($educador) {
                    // Buscar serviços do educador
                    $stmt = $db->prepare("
                        SELECT * FROM servicos WHERE educador_id = ? AND ativo = 1
                    ");
                    $stmt->execute([$id]);
                    $educador['servicos'] = $stmt->fetchAll();
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $educador
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'error' => 'Educador não encontrado']);
                }
            } else {
                // Listar todos os educadores
                $filters = [];
                $params = [];
                
                if (isset($_GET['distrito'])) {
                    $filters[] = "u.distrito = ?";
                    $params[] = $_GET['distrito'];
                }
                if (isset($_GET['aprovado'])) {
                    $filters[] = "e.aprovado = ?";
                    $params[] = $_GET['aprovado'] ? 1 : 0;
                }
                
                $whereClause = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';
                
                $stmt = $db->prepare("
                    SELECT e.id, u.nome, u.email, u.distrito, u.telefone,
                           e.biografia, e.anos_experiencia, e.avaliacao_media, 
                           e.total_avaliacoes, e.aprovado
                    FROM educadores e
                    JOIN utilizadores u ON e.utilizador_id = u.id
                    $whereClause
                    ORDER BY e.avaliacao_media DESC, e.total_avaliacoes DESC
                ");
                $stmt->execute($params);
                $educadores = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $educadores,
                    'total' => count($educadores)
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'POST':
            // Criar novo educador (requer utilizador existente)
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['utilizador_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'utilizador_id é obrigatório']);
                break;
            }
            
            // Verificar se utilizador existe
            $stmt = $db->prepare("SELECT id FROM utilizadores WHERE id = ? AND tipo_utilizador = 'educador'");
            $stmt->execute([$data['utilizador_id']]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Utilizador não encontrado ou não é educador']);
                break;
            }
            
            // Criar educador
            $stmt = $db->prepare("
                INSERT INTO educadores (utilizador_id, biografia, anos_experiencia, certificacoes, aprovado)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['utilizador_id'],
                $data['biografia'] ?? null,
                $data['anos_experiencia'] ?? 0,
                $data['certificacoes'] ?? null,
                isset($data['aprovado']) ? ($data['aprovado'] ? 1 : 0) : 0
            ]);
            
            $educadorId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Educador criado com sucesso',
                'data' => ['id' => (int)$educadorId]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            break;
            
        case 'PUT':
            // Atualizar educador
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'ID é obrigatório']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            $updates = [];
            $params = [];
            
            if (isset($data['biografia'])) {
                $updates[] = "biografia = ?";
                $params[] = $data['biografia'];
            }
            if (isset($data['anos_experiencia'])) {
                $updates[] = "anos_experiencia = ?";
                $params[] = $data['anos_experiencia'];
            }
            if (isset($data['certificacoes'])) {
                $updates[] = "certificacoes = ?";
                $params[] = $data['certificacoes'];
            }
            if (isset($data['aprovado'])) {
                $updates[] = "aprovado = ?";
                $params[] = $data['aprovado'] ? 1 : 0;
            }
            
            if (empty($updates)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Nenhum campo para atualizar']);
                break;
            }
            
            $params[] = $id;
            $sql = "UPDATE educadores SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode([
                'success' => true,
                'message' => 'Educador atualizado com sucesso'
            ]);
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
