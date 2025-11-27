<?php
/**
 * API de Teste de Conexão
 * 
 * Endpoint para verificar se a conexão com o banco de dados está funcionando
 * e retornar estatísticas básicas do sistema
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../bootstrap.php';

try {
    // Testar conexão com o banco
    $db = getDB();
    
    // Obter estatísticas
    $stats = [];
    
    // Total de utilizadores
    $stmt = $db->query("SELECT COUNT(*) as total FROM utilizadores");
    $stats['total_utilizadores'] = (int) $stmt->fetch()['total'];
    
    // Total de educadores
    $stmt = $db->query("SELECT COUNT(*) as total FROM educadores");
    $stats['total_educadores'] = (int) $stmt->fetch()['total'];
    
    // Total de donos
    $stmt = $db->query("SELECT COUNT(*) as total FROM donos");
    $stats['total_donos'] = (int) $stmt->fetch()['total'];
    
    // Total de serviços
    $stmt = $db->query("SELECT COUNT(*) as total FROM servicos");
    $stats['total_servicos'] = (int) $stmt->fetch()['total'];
    
    // Total de agendamentos
    $stmt = $db->query("SELECT COUNT(*) as total FROM agendamentos");
    $stats['total_agendamentos'] = (int) $stmt->fetch()['total'];
    
    // Total de avaliações
    $stmt = $db->query("SELECT COUNT(*) as total FROM avaliacoes");
    $stats['total_avaliacoes'] = (int) $stmt->fetch()['total'];
    
    // Informações adicionais
    $stats['database_name'] = DB_NAME;
    $stats['timestamp'] = date('Y-m-d H:i:s');
    
    // Resposta de sucesso
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Conexão com o banco de dados bem-sucedida',
        'data' => $stats
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Erro de conexão com o banco
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao conectar com o banco de dados',
        'message' => DEBUG_MODE ? $e->getMessage() : 'Erro interno do servidor'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Erro genérico
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'message' => DEBUG_MODE ? $e->getMessage() : 'Erro interno do servidor'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
