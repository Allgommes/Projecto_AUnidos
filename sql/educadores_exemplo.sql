-- ============================================================
-- SCRIPT: Inserir 3 Educadores de Exemplo com Especialidades
-- Data: 12/11/2025
-- Propósito: Popular dashboard com educadores para demonstração
-- ============================================================

-- Educador 1: Maria Silva (Especialista em Obediência)
INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, ativo, email_verificado) 
VALUES (
    'Maria Silva', 
    'maria.silva@exemplo.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: demo123
    'educador', 
    '912345001', 
    'Lisboa', 
    1, 
    1
);

SET @maria_id = LAST_INSERT_ID();

INSERT INTO educadores (utilizador_id, biografia, anos_experiencia, certificacoes, foto_perfil, aprovado, avaliacao_media, total_avaliacoes)
VALUES (
    @maria_id,
    'Especialista em obediência canina com formação internacional. Trabalho com metodologia positiva e tenho experiência com cães de todas as raças e temperamentos.',
    5,
    'Certificação em Treino Canino Positivo, Comportamento Animal (APBC)',
    NULL,
    1,
    4.8,
    127
);

SET @maria_educador_id = LAST_INSERT_ID();

-- Associar especialidades à Maria
INSERT INTO educador_especialidades (educador_id, especialidade_id)
SELECT @maria_educador_id, id FROM especialidades WHERE nome IN ('Obediência Básica', 'Obediência Avançada');

-- Educador 2: João Santos (Especialista em Agility)
INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, ativo, email_verificado) 
VALUES (
    'João Santos', 
    'joao.santos@exemplo.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: demo123
    'educador', 
    '912345002', 
    'Porto', 
    1, 
    1
);

SET @joao_id = LAST_INSERT_ID();

INSERT INTO educadores (utilizador_id, biografia, anos_experiencia, certificacoes, foto_perfil, aprovado, avaliacao_media, total_avaliacoes)
VALUES (
    @joao_id,
    'Treinador certificado em Agility com múltiplos títulos nacionais. Especializado em preparação para competições e treino de alta performance.',
    8,
    'Certificação FCI em Agility, Juiz Nacional de Competições',
    NULL,
    1,
    4.9,
    203
);

SET @joao_educador_id = LAST_INSERT_ID();

-- Associar especialidades ao João
INSERT INTO educador_especialidades (educador_id, especialidade_id)
SELECT @joao_educador_id, id FROM especialidades WHERE nome IN ('Agility', 'Treino para Competições');

-- Educador 3: Ana Costa (Especialista em Guarda e Proteção)
INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, ativo, email_verificado) 
VALUES (
    'Ana Costa', 
    'ana.costa@exemplo.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: demo123
    'educador', 
    '912345003', 
    'Braga', 
    1, 
    1
);

SET @ana_id = LAST_INSERT_ID();

INSERT INTO educadores (utilizador_id, biografia, anos_experiencia, certificacoes, foto_perfil, aprovado, avaliacao_media, total_avaliacoes)
VALUES (
    @ana_id,
    'Especialista em treino de cães de guarda e proteção com 10 anos de experiência. Trabalho também com socialização de cachorros e treino de cães bebé.',
    10,
    'Certificação em Proteção e Guarda K9, Comportamento Canino Avançado',
    NULL,
    1,
    5.0,
    156
);

SET @ana_educador_id = LAST_INSERT_ID();

-- Associar especialidades à Ana
INSERT INTO educador_especialidades (educador_id, especialidade_id)
SELECT @ana_educador_id, id FROM especialidades WHERE nome IN ('Guarda', 'Treino de cães bebé', 'Socialização');

-- Verificar inserções
SELECT 'Educadores criados com sucesso!' as Status;
SELECT u.nome, u.email, u.distrito, e.anos_experiencia, e.avaliacao_media 
FROM utilizadores u 
JOIN educadores e ON u.id = e.utilizador_id 
WHERE u.email IN ('maria.silva@exemplo.com', 'joao.santos@exemplo.com', 'ana.costa@exemplo.com');
