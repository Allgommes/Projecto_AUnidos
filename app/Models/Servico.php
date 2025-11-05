<?php

namespace App\Models;

use PDO;

class Servico {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Criar novo serviço
     */
    public function create($educadorId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO servicos (educador_id, nome, descricao, preco_hora, duracao_minutos, ativo)
                VALUES (?, ?, ?, ?, ?, TRUE)
            ");
            
            $stmt->execute([
                $educadorId,
                $data['nome'],
                $data['descricao'] ?? null,
                $data['preco_hora'],
                $data['duracao_minutos'] ?? 60
            ]);
            
            return ['success' => true, 'id' => $this->db->lastInsertId()];
            
        } catch (\Exception $e) {
            error_log("Erro ao criar serviço: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao criar serviço.'];
        }
    }
    
    /**
     * Atualizar serviço
     */
    public function update($servicoId, $educadorId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE servicos 
                SET nome = ?, descricao = ?, preco_hora = ?, duracao_minutos = ?
                WHERE id = ? AND educador_id = ?
            ");
            
            $stmt->execute([
                $data['nome'],
                $data['descricao'] ?? null,
                $data['preco_hora'],
                $data['duracao_minutos'] ?? 60,
                $servicoId,
                $educadorId
            ]);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro ao atualizar serviço: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao atualizar serviço.'];
        }
    }
    
    /**
     * Obter serviço por ID
     */
    public function getById($servicoId) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, e.id as educador_id, u.nome as educador_nome
                FROM servicos s
                JOIN educadores e ON s.educador_id = e.id
                JOIN utilizadores u ON e.utilizador_id = u.id
                WHERE s.id = ?
            ");
            $stmt->execute([$servicoId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter serviço: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Desativar serviço
     */
    public function deactivate($servicoId, $educadorId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE servicos 
                SET ativo = FALSE 
                WHERE id = ? AND educador_id = ?
            ");
            $stmt->execute([$servicoId, $educadorId]);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro ao desativar serviço: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao desativar serviço.'];
        }
    }
}
