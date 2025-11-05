<?php

namespace App\Models;

use PDO;

class Educador {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Obter educador por ID de utilizador
     */
    public function getByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nome, u.email, u.telefone, u.distrito
                FROM educadores e
                JOIN utilizadores u ON e.utilizador_id = u.id
                WHERE e.utilizador_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter educador: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obter educador por ID
     */
    public function getById($educadorId) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nome, u.email, u.telefone, u.distrito, u.data_criacao,
                       GROUP_CONCAT(DISTINCT esp.nome SEPARATOR ', ') as especialidades
                FROM educadores e
                JOIN utilizadores u ON e.utilizador_id = u.id
                LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
                LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
                WHERE e.id = ? AND e.aprovado = TRUE AND u.ativo = TRUE
                GROUP BY e.id
            ");
            $stmt->execute([$educadorId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter educador: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Pesquisar educadores com filtros
     */
    public function search($filters = []) {
        try {
            $sql = "
                SELECT e.id, e.avaliacao_media, e.total_avaliacoes, e.foto_perfil,
                       u.nome, u.distrito, u.telefone,
                       GROUP_CONCAT(DISTINCT esp.nome SEPARATOR ', ') as especialidades,
                       MIN(s.preco_hora) as preco_minimo
                FROM educadores e
                JOIN utilizadores u ON e.utilizador_id = u.id
                LEFT JOIN educador_especialidades ee ON e.id = ee.educador_id
                LEFT JOIN especialidades esp ON ee.especialidade_id = esp.id
                LEFT JOIN servicos s ON e.id = s.educador_id AND s.ativo = TRUE
                WHERE e.aprovado = TRUE AND u.ativo = TRUE
            ";
            
            $params = [];
            
            // Filtro por distrito
            if (!empty($filters['distrito'])) {
                $sql .= " AND u.distrito = ?";
                $params[] = $filters['distrito'];
            }
            
            // Filtro por especialidade
            if (!empty($filters['especialidade'])) {
                $sql .= " AND esp.id = ?";
                $params[] = $filters['especialidade'];
            }
            
            // Filtro por preço máximo
            if (!empty($filters['preco_max'])) {
                $sql .= " AND s.preco_hora <= ?";
                $params[] = $filters['preco_max'];
            }
            
            // Filtro por avaliação mínima
            if (!empty($filters['avaliacao_min'])) {
                $sql .= " AND e.avaliacao_media >= ?";
                $params[] = $filters['avaliacao_min'];
            }
            
            $sql .= " GROUP BY e.id ORDER BY e.avaliacao_media DESC, e.total_avaliacoes DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Erro ao pesquisar educadores: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Atualizar perfil de educador
     */
    public function updateProfile($educadorId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE educadores 
                SET biografia = ?, anos_experiencia = ?, certificacoes = ?, foto_perfil = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['biografia'] ?? null,
                $data['anos_experiencia'] ?? 0,
                $data['certificacoes'] ?? null,
                $data['foto_perfil'] ?? null,
                $educadorId
            ]);
            
            return ['success' => true];
            
        } catch (\Exception $e) {
            error_log("Erro ao atualizar educador: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erro ao atualizar perfil.'];
        }
    }
    
    /**
     * Obter serviços do educador
     */
    public function getServicos($educadorId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM servicos 
                WHERE educador_id = ? AND ativo = TRUE
                ORDER BY nome
            ");
            $stmt->execute([$educadorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter serviços: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter avaliações do educador
     */
    public function getAvaliacoes($educadorId, $limit = null) {
        try {
            $sql = "
                SELECT a.*, u.nome as cliente_nome, d.utilizador_id
                FROM avaliacoes a
                JOIN agendamentos ag ON a.agendamento_id = ag.id
                JOIN donos d ON ag.dono_id = d.id
                JOIN utilizadores u ON d.utilizador_id = u.id
                WHERE ag.educador_id = ?
                ORDER BY a.data_criacao DESC
            ";
            
            if ($limit) {
                $sql .= " LIMIT " . (int)$limit;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$educadorId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter avaliações: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obter estatísticas do educador
     */
    public function getEstatisticas($educadorId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_agendamentos,
                    SUM(CASE WHEN estado = 'concluido' THEN 1 ELSE 0 END) as agendamentos_concluidos,
                    SUM(CASE WHEN estado = 'pendente' THEN 1 ELSE 0 END) as agendamentos_pendentes
                FROM agendamentos
                WHERE educador_id = ?
            ");
            $stmt->execute([$educadorId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao obter estatísticas: " . $e->getMessage());
            return null;
        }
    }
}
