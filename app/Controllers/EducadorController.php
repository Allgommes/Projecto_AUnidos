<?php

namespace App\Controllers;

use App\Models\Educador;
use App\Models\Servico;

class EducadorController {
    
    /**
     * Listar educadores com filtros
     */
    public function search() {
        $filters = [
            'distrito' => $_GET['distrito'] ?? '',
            'especialidade' => $_GET['especialidade'] ?? '',
            'preco_max' => $_GET['preco_max'] ?? '',
            'avaliacao_min' => $_GET['avaliacao_min'] ?? ''
        ];
        
        $educadorModel = new Educador();
        $educadores = $educadorModel->search($filters);
        
        view('educadores.search', [
            'educadores' => $educadores,
            'filters' => $filters
        ]);
    }
    
    /**
     * Exibir perfil de educador
     */
    public function show() {
        $educadorId = $_GET['id'] ?? 0;
        
        if (!$educadorId) {
            setFlash('error', 'Educador não encontrado.');
            redirect('/buscar-educadores.php');
            return;
        }
        
        $educadorModel = new Educador();
        $educador = $educadorModel->getById($educadorId);
        
        if (!$educador) {
            setFlash('error', 'Educador não encontrado.');
            redirect('/buscar-educadores.php');
            return;
        }
        
        $servicos = $educadorModel->getServicos($educadorId);
        $avaliacoes = $educadorModel->getAvaliacoes($educadorId, 5);
        
        view('educadores.show', [
            'educador' => $educador,
            'servicos' => $servicos,
            'avaliacoes' => $avaliacoes
        ]);
    }
    
    /**
     * Exibir formulário de edição de perfil (educador logado)
     */
    public function editProfile() {
        if (!isAuthenticated() || !isEducador()) {
            redirect('/login.php');
            return;
        }
        
        $educadorModel = new Educador();
        $educador = $educadorModel->getByUserId(authUserId());
        
        if (!$educador) {
            setFlash('error', 'Perfil não encontrado.');
            redirect('/dashboard.php');
            return;
        }
        
        view('educadores.edit', ['educador' => $educador]);
    }
    
    /**
     * Processar atualização de perfil
     */
    public function updateProfile() {
        if (!isAuthenticated() || !isEducador()) {
            redirect('/login.php');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/perfil.php');
            return;
        }
        
        $educadorModel = new Educador();
        $educador = $educadorModel->getByUserId(authUserId());
        
        if (!$educador) {
            setFlash('error', 'Perfil não encontrado.');
            redirect('/dashboard.php');
            return;
        }
        
        $result = $educadorModel->updateProfile($educador['id'], $_POST);
        
        if ($result['success']) {
            setFlash('success', 'Perfil atualizado com sucesso!');
        } else {
            setFlash('error', $result['error'] ?? 'Erro ao atualizar perfil.');
        }
        
        redirect('/perfil.php');
    }
    
    /**
     * Listar serviços do educador
     */
    public function myServices() {
        if (!isAuthenticated() || !isEducador()) {
            redirect('/login.php');
            return;
        }
        
        $educadorModel = new Educador();
        $educador = $educadorModel->getByUserId(authUserId());
        
        if (!$educador) {
            setFlash('error', 'Perfil não encontrado.');
            redirect('/dashboard.php');
            return;
        }
        
        $servicos = $educadorModel->getServicos($educador['id']);
        
        view('educadores.my-services', ['servicos' => $servicos]);
    }
    
    /**
     * Criar novo serviço
     */
    public function createService() {
        if (!isAuthenticated() || !isEducador()) {
            redirect('/login.php');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/meus-servicos.php');
            return;
        }
        
        $educadorModel = new Educador();
        $educador = $educadorModel->getByUserId(authUserId());
        
        if (!$educador) {
            setFlash('error', 'Perfil não encontrado.');
            redirect('/dashboard.php');
            return;
        }
        
        $servicoModel = new Servico();
        $result = $servicoModel->create($educador['id'], $_POST);
        
        if ($result['success']) {
            setFlash('success', 'Serviço criado com sucesso!');
        } else {
            setFlash('error', $result['error'] ?? 'Erro ao criar serviço.');
        }
        
        redirect('/meus-servicos.php');
    }
}
