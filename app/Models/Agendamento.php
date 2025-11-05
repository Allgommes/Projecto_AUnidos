<?php

namespace App\Models;

use PDO;

class Agendamento {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Criar novo agendamento
     */
    public function create($donoId, $educadorId, $servicoId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO agendamentos (dono_id, educador_id, servico_id, data_hora, observacoes, estado)
                VALUES (?, ?, ?, ?, ?, 'pendente')
            ");
            
            $stmt->execute([
                $donoId,
                $educadorId,
                $servicoId,
                $data['data_hora'],
                $data['observacoes'] ?? null
            ]);
            
            $agendamentoId = $this->db->lastInsertId();
            
            // Enviar notificação por email
            $emailService = new \App\Services\EmailService();
            $emailService->sendAgendamentoNotification($agendamentoId);
            
            return ['success' => true, 'id' => $agendamentoId];
            
        } catch (\Exception $e) {
            error_log("Erro ao criar agendamento: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao criar agendamento.'];
        }
    }
    
    /**
     * Obter agendamento por ID
     */
    public function getById($agendamentoId) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       s.nome as servico_nome, s.preco_hora, s.duracao_minutos,
                       ud.nome as dono_nome, ud.email as dono_email, ud.telefone as dono_telefone,
                       ue.nome as educador_nome, ue.email as educador_email, ue.telefone as educador_telefone
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                JOIN donos d ON a.dono_id = d.id
                JOIN utilizadores ud ON d.utilizador_id = ud.id
                JOIN educadores e ON a.educador_id = e.id
                JOIN utilizadores ue ON e.utilizador_id = ue.id
                WHERE a.id = ?
            ");
            $stmt->execute([$agendamentoId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter agendamento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Listar agendamentos de um dono
     */
    public function getByDono($donoId) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       s.nome as servico_nome,
                       ue.nome as educador_nome
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                JOIN educadores e ON a.educador_id = e.id
                JOIN utilizadores ue ON e.utilizador_id = ue.id
                WHERE a.dono_id = ?
                ORDER BY a.data_hora DESC
            ");
            $stmt->execute([$donoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter agendamentos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Listar agendamentos de um educador
     */
    public function getByEducador($educadorId) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, 
                       s.nome as servico_nome,
                       ud.nome as dono_nome, ud.telefone as dono_telefone
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                JOIN donos d ON a.dono_id = d.id
                JOIN utilizadores ud ON d.utilizador_id = ud.id
                WHERE a.educador_id = ?
                ORDER BY a.data_hora DESC
            ");
            $stmt->execute([$educadorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter agendamentos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualizar estado do agendamento
     */
    public function updateEstado($agendamentoId, $novoEstado, $userId) {
        try {
            // Verificar se o agendamento pertence ao utilizador
            $agendamento = $this->getById($agendamentoId);
            if (!$agendamento) {
                return ['success' => false, 'error' => 'Agendamento não encontrado.'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE agendamentos 
                SET estado = ? 
                WHERE id = ?
            ");
            $stmt->execute([$novoEstado, $agendamentoId]);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro ao atualizar estado: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao atualizar estado.'];
        }
    }
    
    /**
     * Cancelar agendamento
     */
    public function cancelar($agendamentoId, $userId) {
        return $this->updateEstado($agendamentoId, 'cancelado', $userId);
    }
    
    /**
     * Confirmar agendamento
     */
    public function confirmar($agendamentoId, $userId) {
        return $this->updateEstado($agendamentoId, 'confirmado', $userId);
    }
    
    /**
     * Concluir agendamento
     */
    public function concluir($agendamentoId, $userId) {
        return $this->updateEstado($agendamentoId, 'concluido', $userId);
    }
}
