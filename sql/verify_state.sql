-- Verificação do estado após correções
-- Execute: mysql -u root aunidos < sql/verify_state.sql

-- 1. Estrutura da tabela de serviços (confirma preco_hora)
SHOW CREATE TABLE servicos; 

-- 2. Últimos serviços criados (preço e duração)
SELECT id, nome, preco_hora, duracao_minutos, ativo, data_criacao
FROM servicos
ORDER BY id DESC
LIMIT 10;

-- 3. Donos com coluna foto_perfil
SELECT u.id, u.nome, d.foto_perfil
FROM utilizadores u
JOIN donos d ON u.id = d.utilizador_id
ORDER BY u.id DESC
LIMIT 10;

-- 4. Educadores com foto_perfil
SELECT u.id, u.nome, e.foto_perfil, e.anos_experiencia
FROM utilizadores u
JOIN educadores e ON u.id = e.utilizador_id
ORDER BY u.id DESC
LIMIT 10;

-- 5. Contagem de perfis sem foto (dono / educador)
SELECT 'donos_sem_foto' AS tipo, COUNT(*) AS total
FROM donos WHERE foto_perfil IS NULL
UNION ALL
SELECT 'educadores_sem_foto', COUNT(*)
FROM educadores WHERE foto_perfil IS NULL;

-- 6. Especialidades associadas a educadores (amostra)
SELECT e.id AS educador_id, u.nome, GROUP_CONCAT(es.nome SEPARATOR ', ') AS especialidades
FROM educadores e
JOIN utilizadores u ON u.id = e.utilizador_id
LEFT JOIN educador_especialidades ee ON ee.educador_id = e.id
LEFT JOIN especialidades es ON es.id = ee.especialidade_id
GROUP BY e.id, u.nome
ORDER BY e.id DESC
LIMIT 5;

-- 7. Verificar se existe algum serviço com preco_hora = 0 (indicaria falha de validação)
SELECT id, nome, preco_hora FROM servicos WHERE preco_hora = 0 LIMIT 5;

-- 8. Sessões não podem ser validadas via SQL; testar manualmente navegação e expiração.
-- Fim
