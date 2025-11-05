<?php
/**
 * API: Testar Conexão com o Banco de Dados
 * GET http://localhost/Projecto_AUnidos/api/test-connection.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../bootstrap.php';

try {
    $db = getDB();
    
    // Testar conexão
    $db->query("SELECT 1");
    
    // Contar registros em cada tabela
    $stats = [];
    
    $tables = ['utilizadores', 'educadores', 'donos', 'servicos', 'agendamentos', 'especialidades'];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) as total FROM $table");
        $result = $stmt->fetch();
        $stats[$table] = (int)$result['total'];
    }
    
    // Informações do banco
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $dbInfo = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conexão com o banco de dados estabelecida com sucesso!',
        'database' => $dbInfo['db_name'],
        'statistics' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
