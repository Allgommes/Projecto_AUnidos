<?php

namespace App\Models;

use PDO;

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Registar novo utilizador
     */
    public function register($data) {
        try {
            // Validar dados
            $errors = $this->validateUserData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            // Verificar se email já existe
            if ($this->emailExists($data['email'])) {
                return ['success' => false, 'errors' => ['Email já está em uso.']];
            }
            
            // Hash da password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            $verificationToken = generateToken();
            
            // Inserir utilizador
            $stmt = $this->db->prepare("
                INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, token_verificacao) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['nome'],
                $data['email'],
                $passwordHash,
                $data['tipo_utilizador'],
                $data['telefone'] ?? null,
                $data['distrito'],
                $verificationToken
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Criar perfil específico baseado no tipo
            if ($data['tipo_utilizador'] === 'educador') {
                $this->createEducadorProfile($userId, $data);
            } else {
                $this->createDonoProfile($userId);
            }
            
            // Enviar email de verificação
            $emailService = new \App\Services\EmailService();
            $emailService->sendVerificationEmail($data['email'], $data['nome'], $verificationToken);
            
            return ['success' => true, 'user_id' => $userId];
            
        } catch (\Exception $e) {
            error_log("Erro no registo: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Erro interno. Tente novamente.']];
        }
    }
    
    /**
     * Login de utilizador
     */
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, email, password_hash, tipo_utilizador, ativo, email_verificado 
                FROM utilizadores 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Credenciais inválidas.'];
            }
            
            if (!$user['ativo']) {
                return ['success' => false, 'error' => 'Conta desativada.'];
            }
            
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Credenciais inválidas.'];
            }
            
            // Iniciar sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['tipo_utilizador'];
            $_SESSION['email_verified'] = $user['email_verificado'];
            
            // Log da ação
            $this->logUserAction($user['id'], 'login', 'Utilizador fez login');
            
            return ['success' => true, 'user' => $user];
            
        } catch (\Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro interno. Tente novamente.'];
        }
    }
    
    /**
     * Verificar email
     */
    public function verifyEmail($token) {
        try {
            $stmt = $this->db->prepare("
                UPDATE utilizadores 
                SET email_verificado = TRUE, token_verificacao = NULL 
                WHERE token_verificacao = ?
            ");
            $stmt->execute([$token]);
            
            return $stmt->rowCount() > 0;
            
        } catch (\Exception $e) {
            error_log("Erro na verificação de email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Recuperação de password
     */
    public function requestPasswordReset($email) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome FROM utilizadores WHERE email = ? AND ativo = TRUE");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Email não encontrado.'];
            }
            
            $resetToken = generateToken();
            
            $stmt = $this->db->prepare("
                UPDATE utilizadores 
                SET token_reset_password = ?, token_reset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                WHERE id = ?
            ");
            $stmt->execute([$resetToken, $user['id']]);
            
            // Enviar email de reset
            $emailService = new \App\Services\EmailService();
            $emailService->sendPasswordResetEmail($email, $user['nome'], $resetToken);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro no reset de password: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro interno. Tente novamente.'];
        }
    }
    
    /**
     * Reset de password
     */
    public function resetPassword($token, $newPassword) {
        try {
            // Validar password
            if (strlen($newPassword) < 6) {
                return ['success' => false, 'error' => 'Password deve ter pelo menos 6 caracteres.'];
            }
            
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                UPDATE utilizadores 
                SET password_hash = ?, token_reset_password = NULL, token_reset_expiry = NULL
                WHERE token_reset_password = ? AND token_reset_expiry > NOW()
            ");
            $stmt->execute([$passwordHash, $token]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true];
            } else {
                return ['success' => false, 'error' => 'Token inválido ou expirado.'];
            }
            
        } catch (\Exception $e) {
            error_log("Erro no reset de password: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro interno. Tente novamente.'];
        }
    }
    
    /**
     * Obter dados do utilizador
     */
    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, email, tipo_utilizador, telefone, distrito, 
                       data_criacao, ativo, email_verificado
                FROM utilizadores 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Erro ao obter utilizador: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualizar perfil do utilizador
     */
    public function updateProfile($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE utilizadores 
                SET nome = ?, telefone = ?, distrito = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nome'],
                $data['telefone'] ?? null,
                $data['distrito'],
                $userId
            ]);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao atualizar perfil.'];
        }
    }
    
    /**
     * Validar dados do utilizador
     */
    private function validateUserData($data) {
        $errors = [];
        
        if (empty($data['nome']) || strlen($data['nome']) < 2) {
            $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Password deve ter pelo menos 6 caracteres.';
        }
        
        if (!empty($data['password']) && $data['password'] !== $data['confirm_password']) {
            $errors[] = 'As passwords não coincidem.';
        }
        
        if (empty($data['tipo_utilizador']) || !in_array($data['tipo_utilizador'], ['dono', 'educador'])) {
            $errors[] = 'Tipo de utilizador inválido.';
        }
        
        if (empty($data['distrito'])) {
            $errors[] = 'Distrito é obrigatório.';
        }
        
        return $errors;
    }
    
    /**
     * Verificar se email existe
     */
    private function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Criar perfil de educador
     */
    private function createEducadorProfile($userId, $data) {
        $stmt = $this->db->prepare("
            INSERT INTO educadores (utilizador_id, biografia, anos_experiencia) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $data['biografia'] ?? null,
            $data['anos_experiencia'] ?? 0
        ]);
    }
    
    /**
     * Criar perfil de dono
     */
    private function createDonoProfile($userId) {
        $stmt = $this->db->prepare("
            INSERT INTO donos (utilizador_id) VALUES (?)
        ");
        $stmt->execute([$userId]);
    }
    
    /**
     * Log de ações do utilizador
     */
    private function logUserAction($userId, $action, $description = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO logs_sistema (utilizador_id, acao, descricao, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (\Exception $e) {
            error_log("Erro ao registar log: " . $e->getMessage());
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logUserAction($_SESSION['user_id'], 'logout', 'Utilizador fez logout');
        }
        
        session_destroy();
        return true;
    }
}
