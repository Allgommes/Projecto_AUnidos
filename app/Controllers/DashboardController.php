<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Educador;
use App\Models\Agendamento;

class DashboardController {
    
    /**
     * Exibir dashboard principal
     */
    public function index() {
        if (!isAuthenticated()) {
            redirect('/login.php');
            return;
        }
        
        $userModel = new User();
        $user = $userModel->getUserById(authUserId());
        
        if (!$user) {
            redirect('/logout.php');
            return;
        }
        
        // Dashboard diferente para educador e dono
        if (isEducador()) {
            $this->educadorDashboard($user);
        } else {
            $this->donoDashboard($user);
        }
    }
    
    /**
     * Dashboard do educador
     */
    private function educadorDashboard($user) {
        $educadorModel = new Educador();
        $educador = $educadorModel->getByUserId($user['id']);
        
        if (!$educador) {
            setFlash('error', 'Perfil de educador não encontrado.');
            redirect('/logout.php');
            return;
        }
        
        $agendamentoModel = new Agendamento();
        $agendamentos = $agendamentoModel->getByEducador($educador['id']);
        $estatisticas = $educadorModel->getEstatisticas($educador['id']);
        $servicos = $educadorModel->getServicos($educador['id']);
        
        view('dashboard.educador', [
            'user' => $user,
            'educador' => $educador,
            'agendamentos' => $agendamentos,
            'estatisticas' => $estatisticas,
            'servicos' => $servicos
        ]);
    }
    
    /**
     * Dashboard do dono
     */
    private function donoDashboard($user) {
        // Obter ID do dono
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM donos WHERE utilizador_id = ?");
        $stmt->execute([$user['id']]);
        $dono = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$dono) {
            setFlash('error', 'Perfil de dono não encontrado.');
            redirect('/logout.php');
            return;
        }
        
        $agendamentoModel = new Agendamento();
        $agendamentos = $agendamentoModel->getByDono($dono['id']);
        
        view('dashboard.dono', [
            'user' => $user,
            'agendamentos' => $agendamentos
        ]);
    }
}
