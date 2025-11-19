<?php
/**
 * Bootstrap da Aplicação
 * Este arquivo inicializa a aplicação e carrega todas as dependências necessárias
 */

// Carregar o autoloader do Composer e configurações
require_once __DIR__ . '/config/database.php';
// Carregar helpers globais
require_once __DIR__ . '/app/Helpers/functions.php';

// Timezone padrão
date_default_timezone_set('Europe/Lisbon');

// Configurações de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Reforço de sessão (sem alterar fluxo de login/registro já funcional)
if (session_status() === PHP_SESSION_NONE) {
    // Parâmetros já definidos em database.php; garantir início
    session_start();
}
// Regenerar ID de sessão periodicamente para mitigar fixação
if (!isset($_SESSION['__regenerated_at'])) {
    $_SESSION['__regenerated_at'] = time();
} elseif (time() - $_SESSION['__regenerated_at'] > 600) { // 10 minutos
    session_regenerate_id(true);
    $_SESSION['__regenerated_at'] = time();
}

// Função auxiliar para incluir views
if (!function_exists('renderView')) {
    function renderView($viewPath, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/resources/views/' . $viewPath . '.php';
        
        if (!file_exists($viewFile)) {
            die("View não encontrada: $viewPath");
        }
        
        require $viewFile;
    }
}
