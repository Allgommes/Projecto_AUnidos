<?php
/**
 * Configuração da Base de Dados
 * 
 * Este arquivo carrega as variáveis de ambiente do arquivo .env
 * e configura a conexão com o banco de dados
 */

// Carregar o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar variáveis de ambiente
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Se o arquivo .env não existir, usar valores padrão
    error_log("Arquivo .env não encontrado: " . $e->getMessage());
}

// Configurações da Base de Dados
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'aunidos');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

// URLs do Site
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost/Projecto_AUnidos');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Configurações de Segurança
define('SESSION_LIFETIME', 3600);
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300);

// Configurações de Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);

// Configurações de Debug
define('DEBUG_MODE', $_ENV['DEBUG_MODE'] ?? true);
define('ERROR_REPORTING', E_ALL);

// Configurar relatório de erros
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(ERROR_REPORTING);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

/**
 * Função para obter conexão com o banco de dados
 * @return PDO Conexão PDO com o banco de dados
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
            
            if (DEBUG_MODE) {
                die("❌ Erro de conexão com o banco de dados: " . $e->getMessage());
            } else {
                die("❌ Erro de conexão com o banco de dados. Por favor, tente novamente mais tarde.");
            }
        }
    }
    
    return $db;
}

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar tempo de vida da sessão ANTES de iniciar
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    
    // Agora sim iniciar a sessão
    session_start();
}