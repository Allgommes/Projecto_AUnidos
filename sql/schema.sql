-- Criação da base de dados AUnidos
CREATE DATABASE IF NOT EXISTS aunidos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aunidos;

-- Tabela de utilizadores (donos de cães e educadores)
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    tipo_utilizador ENUM('dono', 'educador') NOT NULL,
    telefone VARCHAR(20),
    distrito VARCHAR(50) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    token_verificacao VARCHAR(255),
    token_reset_password VARCHAR(255),
    INDEX idx_email (email),
    INDEX idx_tipo (tipo_utilizador),
    INDEX idx_distrito (distrito)
);

-- Tabela específica para perfis de educadores
CREATE TABLE educadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    anos_experiencia INT DEFAULT 0,
    biografia TEXT,
    certificacoes TEXT,
    preco_minimo DECIMAL(10,2),
    preco_maximo DECIMAL(10,2),
    disponibilidade TEXT, -- JSON com horários disponíveis
    foto_perfil VARCHAR(255),
    aprovado BOOLEAN DEFAULT FALSE,
    data_aprovacao TIMESTAMP NULL,
    avaliacao_media DECIMAL(3,2) DEFAULT 0.00,
    total_avaliacoes INT DEFAULT 0,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    INDEX idx_utilizador (utilizador_id),
    INDEX idx_aprovado (aprovado),
    INDEX idx_avaliacao (avaliacao_media)
);

-- Tabela de especialidades
CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    INDEX idx_nome (nome)
);

-- Inserir especialidades padrão
INSERT INTO especialidades (nome, descricao) VALUES
('Obediência', 'Treino básico de comandos e obediência'),
('Agility', 'Treino de agilidade e obstáculos'),
('Modificação de Comportamento', 'Correção de problemas comportamentais'),
('Treino de Cachorros', 'Especialização em treino de cães jovens'),
('Treino de Cães de Serviço', 'Treino para cães de assistência e serviço');

-- Tabela de relacionamento entre educadores e especialidades
CREATE TABLE educador_especialidades (
    educador_id INT NOT NULL,
    especialidade_id INT NOT NULL,
    PRIMARY KEY (educador_id, especialidade_id),
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id) ON DELETE CASCADE
);

-- Tabela de serviços oferecidos pelos educadores
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    educador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    duracao_minutos INT DEFAULT 60,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    INDEX idx_educador (educador_id),
    INDEX idx_ativo (ativo),
    INDEX idx_preco (preco)
);

-- Tabela de perfis de donos de cães
CREATE TABLE donos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    foto_perfil VARCHAR(255),
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    INDEX idx_utilizador (utilizador_id)
);

-- Tabela de cães
CREATE TABLE caes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dono_id INT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    raca VARCHAR(100),
    idade INT,
    peso DECIMAL(5,2),
    temperamento TEXT,
    necessidades_especiais TEXT,
    foto VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dono_id) REFERENCES donos(id) ON DELETE CASCADE,
    INDEX idx_dono (dono_id),
    INDEX idx_ativo (ativo)
);

-- Tabela de agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dono_id INT NOT NULL,
    educador_id INT NOT NULL,
    servico_id INT NOT NULL,
    cao_id INT,
    data_agendamento DATETIME NOT NULL,
    duracao_minutos INT DEFAULT 60,
    status ENUM('pendente', 'confirmado', 'em_andamento', 'concluido', 'cancelado') DEFAULT 'pendente',
    preco DECIMAL(10,2) NOT NULL,
    notas TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dono_id) REFERENCES donos(id) ON DELETE CASCADE,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    FOREIGN KEY (cao_id) REFERENCES caes(id) ON DELETE SET NULL,
    INDEX idx_dono (dono_id),
    INDEX idx_educador (educador_id),
    INDEX idx_data (data_agendamento),
    INDEX idx_status (status)
);

-- Tabela de avaliações
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL,
    dono_id INT NOT NULL,
    educador_id INT NOT NULL,
    nota INT NOT NULL CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (dono_id) REFERENCES donos(id) ON DELETE CASCADE,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    INDEX idx_educador (educador_id),
    INDEX idx_nota (nota),
    INDEX idx_data (data_criacao)
);

-- Tabela de mensagens/chat
CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remetente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    agendamento_id INT,
    conteudo TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remetente_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE SET NULL,
    INDEX idx_remetente (remetente_id),
    INDEX idx_destinatario (destinatario_id),
    INDEX idx_data (data_criacao),
    INDEX idx_lida (lida)
);

-- Tabela de notificações
CREATE TABLE notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    tipo ENUM('novo_agendamento', 'agendamento_confirmado', 'agendamento_cancelado', 'nova_mensagem', 'nova_avaliacao') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    conteudo TEXT,
    lida BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE,
    INDEX idx_utilizador (utilizador_id),
    INDEX idx_lida (lida),
    INDEX idx_data (data_criacao)
);

-- Tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL,
    INDEX idx_utilizador (utilizador_id),
    INDEX idx_acao (acao),
    INDEX idx_data (data_criacao)
);

-- Trigger para atualizar a média de avaliações dos educadores
DELIMITER //
CREATE TRIGGER atualizar_avaliacao_educador 
AFTER INSERT ON avaliacoes
FOR EACH ROW 
BEGIN
    UPDATE educadores 
    SET 
        avaliacao_media = (
            SELECT AVG(nota) 
            FROM avaliacoes 
            WHERE educador_id = NEW.educador_id
        ),
        total_avaliacoes = (
            SELECT COUNT(*) 
            FROM avaliacoes 
            WHERE educador_id = NEW.educador_id
        )
    WHERE id = NEW.educador_id;
END//
DELIMITER ;

-- Criação de utilizador administrador padrão
INSERT INTO utilizadores (nome, email, password_hash, tipo_utilizador, telefone, distrito, ativo, email_verificado) 
VALUES ('Administrador', 'admin@aunidos.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'educador', '+351912345678', 'Lisboa', TRUE, TRUE);

-- Criar perfil de educador para o administrador
INSERT INTO educadores (utilizador_id, anos_experiencia, biografia, aprovado, avaliacao_media) 
VALUES (1, 15, 'Administrador da plataforma AUnidos com vasta experiência em treino canino.', TRUE, 5.00);