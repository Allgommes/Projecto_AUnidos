<?php

/**
 * Helper Functions
 * Funções auxiliares utilizadas em toda a aplicação
 */

/**
 * Gera um token aleatório seguro
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Redireciona para uma URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Retorna a URL base da aplicação
 */
function baseUrl($path = '') {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $scriptPath = rtrim($scriptPath, '/');
    
    return $protocol . $host . $scriptPath . '/' . ltrim($path, '/');
}

/**
 * Retorna a URL do asset (css, js, images)
 */
function asset($path) {
    return baseUrl('public/' . ltrim($path, '/'));
}

/**
 * Sanitiza output HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Verifica se o usuário está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Retorna o ID do usuário autenticado
 */
function authUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Retorna o tipo de usuário autenticado
 */
function authUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Verifica se o usuário é educador
 */
function isEducador() {
    return authUserType() === 'educador';
}

/**
 * Verifica se o usuário é dono
 */
function isDono() {
    return authUserType() === 'dono';
}

/**
 * Define uma mensagem flash
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Obtém e remove uma mensagem flash
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Verifica se existe uma mensagem flash
 */
function hasFlash($key) {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Formata uma data para o formato português
 */
function formatDate($date, $format = 'd/m/Y') {
    if (!$date) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Formata uma data e hora para o formato português
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (!$datetime) return '';
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    return date($format, $timestamp);
}

/**
 * Retorna o valor antigo de um campo de formulário (útil após validação)
 */
function old($key, $default = '') {
    return $_POST[$key] ?? $default;
}

/**
 * Debug helper - exibe variável e para a execução
 */
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Carrega uma view
 */
function view($viewPath, $data = []) {
    extract($data);
    $viewFile = __DIR__ . '/../../resources/views/' . str_replace('.', '/', $viewPath) . '.php';
    
    if (!file_exists($viewFile)) {
        die("View não encontrada: $viewPath");
    }
    
    require $viewFile;
}

/**
 * Carrega um layout
 */
function layout($layoutName, $data = []) {
    extract($data);
    $layoutFile = __DIR__ . '/../../resources/views/layouts/' . $layoutName . '.php';
    
    if (!file_exists($layoutFile)) {
        die("Layout não encontrado: $layoutName");
    }
    
    require $layoutFile;
}
