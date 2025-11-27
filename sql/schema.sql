```sql

-- Criar tabela de utilizadores
CREATE TABLE utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    tipo_utilizador ENUM('dono', 'educador') NOT NULL,
    telefone VARCHAR(20),
    distrito VARCHAR(50) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    token_verificacao VARCHAR(64),
    token_reset_password VARCHAR(64),
    token_reset_expiry DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de educadores
CREATE TABLE educadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT UNIQUE NOT NULL,
    biografia TEXT,
    anos_experiencia INT DEFAULT 0,
    certificacoes TEXT,
    foto_perfil VARCHAR(255),
    aprovado BOOLEAN DEFAULT FALSE,
    avaliacao_media DECIMAL(3,2) DEFAULT 0.00,
    total_avaliacoes INT DEFAULT 0,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Criar tabela de donos
CREATE TABLE donos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE CASCADE
);

-- Criar tabela de serviços
CREATE TABLE servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    educador_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco_hora DECIMAL(10,2) NOT NULL,
    duracao_minutos INT DEFAULT 60,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE
);

-- Criar tabela de agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dono_id INT NOT NULL,
    educador_id INT NOT NULL,
    servico_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    observacoes TEXT,
    estado ENUM('pendente', 'confirmado', 'cancelado', 'concluido') DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dono_id) REFERENCES donos(id) ON DELETE CASCADE,
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE
);

-- Criar tabela de especialidades
CREATE TABLE especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL
);

-- Criar tabela de relação educador-especialidades
CREATE TABLE educador_especialidades (
    educador_id INT NOT NULL,
    especialidade_id INT NOT NULL,
    PRIMARY KEY (educador_id, especialidade_id),
    FOREIGN KEY (educador_id) REFERENCES educadores(id) ON DELETE CASCADE,
    FOREIGN KEY (especialidade_id) REFERENCES especialidades(id) ON DELETE CASCADE
);

-- Criar tabela de avaliações
CREATE TABLE avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT UNIQUE NOT NULL,
    avaliacao INT NOT NULL CHECK (avaliacao BETWEEN 1 AND 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE
);

-- Criar tabela de logs do sistema
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT,
    acao VARCHAR(50) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) ON DELETE SET NULL
);

-- Inserir especialidades padrão
INSERT INTO especialidades (nome) VALUES
('Obediência Básica'),
('Adestramento Avançado'),
('Socialização'),
('Correção de Comportamento'),
('Treino para Competições'),
('Treino de Cães de Guarda'),
('Treino de Cães de Assistência'),
('Treino Anti-Puxar'),
('Passeios Educativos'),
('Consultoria Comportamental');