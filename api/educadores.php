<?php
/**
 * API de Educadores
 * 
 * Endpoints:
 * - GET: Listar todos ou buscar por ID/distrito/especialidade
 * - POST: Criar novo educador
 * - PUT: Atualizar educador existente
 * - DELETE: Remover educador
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGet($db);
            break;
            
        case 'POST':
            handlePost($db);
            break;
            
        case 'PUT':
            handlePut($db);
            break;
            
        case 'DELETE':
            handleDelete($db);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Método não permitido'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'message' => DEBUG_MODE ? $e->getMessage() : 'Erro interno do servidor'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * GET - Listar educadores
 */
function handleGet($db) {
    $id = $_GET['id'] ?? null;
    $distrito = $_GET['distrito'] ?? null;
    $especialidade = $_GET['especialidade'] ?? null;
    
    if ($id) {
        // Buscar por ID
        $stmt = $db->prepare("
            SELECT e.*, u.nome, u.email, u.telefone, u.distrito,
                   GROUP_CONCAT(DISTINCT es.nome SEPARATOR ', ') as especialidades
            FROM educadores e
            JOIN utilizadores u ON e.utilizador_id = u.id
            LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
            LEFT JOIN especialidades es ON ee.especialidade_id = es.id
            WHERE e.id = ?
            GROUP BY e.id
        ");
        $stmt->execute([$id]);
        $educador = $stmt->fetch();
        
        if ($educador) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $educador
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Educador não encontrado'
            ]);
        }
        
    } elseif ($distrito) {
        // Buscar por distrito
        $stmt = $db->prepare("
            SELECT e.*, u.nome, u.email, u.telefone, u.distrito,
                   GROUP_CONCAT(DISTINCT es.nome SEPARATOR ', ') as especialidades
            FROM educadores e
            JOIN utilizadores u ON e.utilizador_id = u.id
            LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
            LEFT JOIN especialidades es ON ee.especialidade_id = es.id
            WHERE u.distrito = ?
            GROUP BY e.id
            ORDER BY e.avaliacao_media DESC
        ");
        $stmt->execute([$distrito]);
        $educadores = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $educadores
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } elseif ($especialidade) {
        // Buscar por especialidade
        $stmt = $db->prepare("
            SELECT e.*, u.nome, u.email, u.telefone, u.distrito,
                   GROUP_CONCAT(DISTINCT es.nome SEPARATOR ', ') as especialidades
            FROM educadores e
            JOIN utilizadores u ON e.utilizador_id = u.id
            LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
            LEFT JOIN especialidades es ON ee.especialidade_id = es.id
            WHERE e.id IN (
                SELECT DISTINCT ee2.educador_id 
                FROM educador_especialidades ee2
                JOIN especialidades es2 ON ee2.especialidade_id = es2.id
                WHERE es2.nome = ?
            )
            GROUP BY e.id
            ORDER BY e.avaliacao_media DESC
        ");
        $stmt->execute([$especialidade]);
        $educadores = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $educadores
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        // Listar todos
        $stmt = $db->query("
            SELECT e.*, u.nome, u.email, u.telefone, u.distrito,
                   GROUP_CONCAT(DISTINCT es.nome SEPARATOR ', ') as especialidades
            FROM educadores e
            JOIN utilizadores u ON e.utilizador_id = u.id
            LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
            LEFT JOIN especialidades es ON ee.especialidade_id = es.id
            GROUP BY e.id
            ORDER BY e.avaliacao_media DESC
        ");
        $educadores = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $educadores
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * POST - Criar educador
 */
function handlePost($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['utilizador_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Campo utilizador_id é obrigatório'
        ]);
        return;
    }
    
    $stmt = $db->prepare("
        INSERT INTO educadores (utilizador_id, biografia, anos_experiencia, certificacoes, foto_perfil)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['utilizador_id'],
        $data['biografia'] ?? null,
        $data['anos_experiencia'] ?? 0,
        $data['certificacoes'] ?? null,
        $data['foto_perfil'] ?? null
    ]);
    
    $educadorId = $db->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Educador criado com sucesso',
        'data' => [
            'id' => $educadorId,
            'utilizador_id' => $data['utilizador_id']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * PUT - Atualizar educador
 */
function handlePut($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Campo id é obrigatório'
        ]);
        return;
    }
    
    $stmt = $db->prepare("
        UPDATE educadores 
        SET biografia = ?, 
            anos_experiencia = ?, 
            certificacoes = ?,
            foto_perfil = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $data['biografia'] ?? null,
        $data['anos_experiencia'] ?? 0,
        $data['certificacoes'] ?? null,
        $data['foto_perfil'] ?? null,
        $data['id']
    ]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Educador atualizado com sucesso'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Educador não encontrado ou nenhuma alteração foi feita'
        ]);
    }
}

/**
 * DELETE - Remover educador
 */
function handleDelete($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Campo id é obrigatório'
        ]);
        return;
    }
    
    $stmt = $db->prepare("DELETE FROM educadores WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Educador removido com sucesso'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Educador não encontrado'
        ]);
    }
}
