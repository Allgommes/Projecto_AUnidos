<?php
/**
 * API de Serviços
 * 
 * Endpoints:
 * - GET: Listar todos ou buscar por ID
 * - POST: Criar novo serviço
 * - PUT: Atualizar serviço existente
 * - DELETE: Remover serviço
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
 * GET - Listar serviços
 */
function handleGet($db) {
    $id = $_GET['id'] ?? null;
    $educador_id = $_GET['educador_id'] ?? null;
    
    if ($id) {
        // Buscar por ID
        $stmt = $db->prepare("
            SELECT s.*, u.nome as educador_nome, u.distrito
            FROM servicos s
            JOIN educadores e ON s.educador_id = e.id
            JOIN utilizadores u ON e.utilizador_id = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $servico = $stmt->fetch();
        
        if ($servico) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $servico
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Serviço não encontrado'
            ]);
        }
        
    } elseif ($educador_id) {
        // Buscar por educador
        $stmt = $db->prepare("
            SELECT s.*, u.nome as educador_nome, u.distrito
            FROM servicos s
            JOIN educadores e ON s.educador_id = e.id
            JOIN utilizadores u ON e.utilizador_id = u.id
            WHERE s.educador_id = ?
            ORDER BY s.nome
        ");
        $stmt->execute([$educador_id]);
        $servicos = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $servicos
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        // Listar todos
        $stmt = $db->query("
            SELECT s.*, u.nome as educador_nome, u.distrito
            FROM servicos s
            JOIN educadores e ON s.educador_id = e.id
            JOIN utilizadores u ON e.utilizador_id = u.id
            WHERE s.ativo = 1
            ORDER BY s.data_criacao DESC
        ");
        $servicos = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $servicos
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

/**
 * POST - Criar serviço
 */
function handlePost($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validações
    $required = ['educador_id', 'nome', 'descricao', 'preco_hora', 'duracao_minutos'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Campo {$field} é obrigatório"
            ]);
            return;
        }
    }
    
    // Validar preço e duração
    if ($data['preco_hora'] < 0.01) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'O preço deve ser maior que €0.01'
        ]);
        return;
    }
    
    if ($data['duracao_minutos'] < 15) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'A duração deve ser pelo menos 15 minutos'
        ]);
        return;
    }
    
    $stmt = $db->prepare("
        INSERT INTO servicos (educador_id, nome, descricao, preco_hora, duracao_minutos)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['educador_id'],
        $data['nome'],
        $data['descricao'],
        number_format($data['preco_hora'], 2, '.', ''),
        $data['duracao_minutos']
    ]);
    
    $servicoId = $db->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Serviço criado com sucesso',
        'data' => [
            'id' => $servicoId,
            'educador_id' => $data['educador_id'],
            'nome' => $data['nome']
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * PUT - Atualizar serviço
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
    
    // Validar preço se fornecido
    if (isset($data['preco_hora']) && $data['preco_hora'] < 0.01) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'O preço deve ser maior que €0.01'
        ]);
        return;
    }
    
    // Validar duração se fornecida
    if (isset($data['duracao_minutos']) && $data['duracao_minutos'] < 15) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'A duração deve ser pelo menos 15 minutos'
        ]);
        return;
    }
    
    $stmt = $db->prepare("
        UPDATE servicos 
        SET nome = COALESCE(?, nome),
            descricao = COALESCE(?, descricao),
            preco_hora = COALESCE(?, preco_hora),
            duracao_minutos = COALESCE(?, duracao_minutos),
            ativo = COALESCE(?, ativo)
        WHERE id = ?
    ");
    
    $stmt->execute([
        $data['nome'] ?? null,
        $data['descricao'] ?? null,
        isset($data['preco_hora']) ? number_format($data['preco_hora'], 2, '.', '') : null,
        $data['duracao_minutos'] ?? null,
        $data['ativo'] ?? null,
        $data['id']
    ]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Serviço atualizado com sucesso'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Serviço não encontrado ou nenhuma alteração foi feita'
        ]);
    }
}

/**
 * DELETE - Remover serviço
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
    
    $stmt = $db->prepare("DELETE FROM servicos WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Serviço removido com sucesso'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Serviço não encontrado'
        ]);
    }
}
