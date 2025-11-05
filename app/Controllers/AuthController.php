<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    
    /**
     * Exibir formulário de registro
     */
    public function showRegister() {
        $distritos = [
            'Aveiro', 'Beja', 'Braga', 'Bragança', 'Castelo Branco', 'Coimbra',
            'Évora', 'Faro', 'Guarda', 'Leiria', 'Lisboa', 'Portalegre',
            'Porto', 'Santarém', 'Setúbal', 'Viana do Castelo', 'Vila Real', 'Viseu'
        ];
        
        view('auth.register', ['distritos' => $distritos]);
    }
    
    /**
     * Processar registro
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register.php');
            return;
        }
        
        $user = new User();
        $result = $user->register($_POST);
        
        if ($result['success']) {
            setFlash('success', 'Registo efetuado com sucesso! Verifique o seu email para ativar a conta.');
            redirect('/login.php');
        } else {
            setFlash('errors', $result['errors'] ?? [$result['error'] ?? 'Erro no registo.']);
            redirect('/register.php');
        }
    }
    
    /**
     * Exibir formulário de login
     */
    public function showLogin() {
        view('auth.login');
    }
    
    /**
     * Processar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login.php');
            return;
        }
        
        $user = new User();
        $result = $user->login($_POST['email'], $_POST['password']);
        
        if ($result['success']) {
            setFlash('success', 'Login efetuado com sucesso!');
            redirect('/dashboard.php');
        } else {
            setFlash('error', $result['error'] ?? 'Erro no login.');
            redirect('/login.php');
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        $user = new User();
        $user->logout();
        redirect('/index.php');
    }
    
    /**
     * Exibir formulário de recuperação de password
     */
    public function showForgotPassword() {
        view('auth.forgot-password');
    }
    
    /**
     * Processar recuperação de password
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/forgot-password.php');
            return;
        }
        
        $user = new User();
        $result = $user->requestPasswordReset($_POST['email']);
        
        if ($result['success']) {
            setFlash('success', 'Enviámos um email com instruções para recuperar a sua password.');
        } else {
            setFlash('error', $result['error'] ?? 'Erro ao processar pedido.');
        }
        
        redirect('/forgot-password.php');
    }
    
    /**
     * Exibir formulário de reset de password
     */
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            setFlash('error', 'Token inválido.');
            redirect('/login.php');
            return;
        }
        
        view('auth.reset-password', ['token' => $token]);
    }
    
    /**
     * Processar reset de password
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login.php');
            return;
        }
        
        $user = new User();
        $result = $user->resetPassword($_POST['token'], $_POST['password']);
        
        if ($result['success']) {
            setFlash('success', 'Password alterada com sucesso! Pode agora fazer login.');
            redirect('/login.php');
        } else {
            setFlash('error', $result['error'] ?? 'Erro ao alterar password.');
            redirect('/reset-password.php?token=' . $_POST['token']);
        }
    }
    
    /**
     * Verificar email
     */
    public function verifyEmail() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            setFlash('error', 'Token inválido.');
            redirect('/login.php');
            return;
        }
        
        $user = new User();
        $success = $user->verifyEmail($token);
        
        if ($success) {
            setFlash('success', 'Email verificado com sucesso! Pode agora fazer login.');
        } else {
            setFlash('error', 'Token inválido ou expirado.');
        }
        
        redirect('/login.php');
    }
}
