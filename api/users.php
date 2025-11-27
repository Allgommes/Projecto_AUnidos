<?php
/**
 * API de Utilizadores
 * 
 * Endpoints:
 * - GET: Listar todos ou buscar por ID/tipo
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    
    if ($method === 'GET') {
        handleGet($db);
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Método não permitido. Use GET.'
        ]);
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
 * GET - Listar utilizadores
 */
function handleGet($db) {
    $id = $_GET['id'] ?? null;
    $tipo = $_GET['tipo'] ?? null;
    
    if ($id) {
        // Buscar por ID
        $stmt = $db->prepare("
            SELECT id, nome, email, telefone, distrito, tipo_utilizador, 
                   ativo, email_verificado, data_criacao
            FROM utilizadores
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $utilizador = $stmt->fetch();
        
        if ($utilizador) {
            // Adicionar informações específicas do tipo
            if ($utilizador['tipo_utilizador'] === 'educador') {
                $stmt = $db->prepare("
                    SELECT e.*, GROUP_CONCAT(DISTINCT es.nome SEPARATOR ', ') as especialidades
                    FROM educadores e
                    LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
                    LEFT JOIN especialidades es ON ee.especialidade_id = es.id
                    WHERE e.utilizador_id = ?
                    GROUP BY e.id
                ");
                $stmt->execute([$id]);
                $utilizador['educador_info'] = $stmt->fetch();
            } elseif ($utilizador['tipo_utilizador'] === 'dono') {
                $stmt = $db->prepare("SELECT * FROM donos WHERE utilizador_id = ?");
                $stmt->execute([$id]);
                $utilizador['dono_info'] = $stmt->fetch();
            }
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $utilizador
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Utilizador não encontrado'
            ]);
        }
        
    } elseif ($tipo) {
        // Buscar por tipo
        $stmt = $db->prepare("
            SELECT id, nome, email, telefone, distrito, tipo_utilizador, 
                   ativo, email_verificado, data_criacao
            FROM utilizadores
            WHERE tipo_utilizador = ?
            ORDER BY data_criacao DESC
        ");
        $stmt->execute([$tipo]);
        $utilizadores = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $utilizadores
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        // Listar todos
        $stmt = $db->query("
            SELECT id, nome, email, telefone, distrito, tipo_utilizador, 
                   ativo, email_verificado, data_criacao
            FROM utilizadores
            WHERE ativo = 1
            ORDER BY data_criacao DESC
        ");
        $utilizadores = $stmt->fetchAll();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $utilizadores,
            'total' => count($utilizadores)
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
