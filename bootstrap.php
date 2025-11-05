<?php
/**
 * Bootstrap da Aplicação
 * Este arquivo inicializa a aplicação e carrega todas as dependências necessárias
 */

// Carregar o autoloader do Composer e configurações
require_once __DIR__ . '/config/database.php';

// Timezone padrão
date_default_timezone_set('Europe/Lisbon');

// Configurações de segurança
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

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
