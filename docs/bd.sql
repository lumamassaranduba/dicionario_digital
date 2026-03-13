-- Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS dicionario_tecnico;
USE dicionario_tecnico;

-- 1. Tabela de Categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

-- 1.5. Tabela de Turmas
CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(50) NOT NULL,
    categoria_id INT NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 2. Tabela de Usuários (AGORA COM A COLUNA EMAIL)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL, 
    email VARCHAR(100) UNIQUE NOT NULL, -- Nova coluna para o usuário conseguir fazer login!
    tipo ENUM('aluno', 'professor') NOT NULL, 
    senha VARCHAR(50) NOT NULL,
    categoria_id INT NULL, -- Amarra o professor à matéria dele
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 3. Tabela de Termos (Continua perfeita)
CREATE TABLE termos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    exemplo TEXT NULL,
    imagem VARCHAR(255) NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    categoria_id INT NOT NULL,
    usuario_id INT NOT NULL, 
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ==========================================
-- DADOS INICIAIS DE TESTE
-- ==========================================

INSERT INTO categorias (nome) VALUES ('Português'), ('Matemática');

-- Inserindo turmas
INSERT INTO turmas (nome, email, senha, categoria_id) VALUES 
('Sala 1A', 'sala1a@senai.br', 'sala1a', 1), 
('Sala 2B', 'sala2b@senai.br', 'sala2b', 2);

-- Inserindo usuários (Agora com um e-mail/login curto e fácil para testar)
INSERT INTO usuarios (nome, email, tipo, senha, categoria_id) VALUES 
('Professor de Português', 'profport@senai.br', 'professor', 'senha123', 1), 
('Professor de Matemática', 'profmat@senai.br', 'professor', 'senha123', 2), 
('Sala 1A', 'sala1a@senai.br', 'aluno', 'sala1a', NULL), 
('Sala 2B', 'sala2b@senai.br', 'aluno', 'sala2b', NULL);

-- Inserindo termos
INSERT INTO termos (palavra, descricao, status, categoria_id, usuario_id) VALUES 
('Sintaxe', 'Estudo da disposição das palavras.', 'aprovado', 1, 3), 
('Equação', 'Sentença matemática.', 'pendente', 2, 4);