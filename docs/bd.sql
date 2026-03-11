-- Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS dicionario_tecnico;
USE dicionario_tecnico;

-- 1. Tabela de Categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

-- 2. Tabela de Usuários (AGORA COM A MATÉRIA DO PROFESSOR)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL, 
    tipo ENUM('aluno', 'professor') NOT NULL, 
    senha VARCHAR(50) NOT NULL,
    categoria_id INT NULL, -- Nova coluna! (Pode ser NULL para alunos)
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 3. Tabela de Termos (Continua igual)
CREATE TABLE termos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    palavra VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
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

-- Inserindo usuários (Agora o professor está amarrado à matéria dele)
INSERT INTO usuarios (nome, tipo, senha, categoria_id) VALUES 
('Professor de Português', 'professor', 'senha123', 1), -- ID 1 = Português
('Professor de Matemática', 'professor', 'senha123', 2), -- ID 2 = Matemática
('Sala 1A', 'aluno', 'sala1a', NULL), -- Aluno não tem categoria específica
('Sala 2B', 'aluno', 'sala2b', NULL);

-- Inserindo termos
INSERT INTO termos (palavra, descricao, status, categoria_id, usuario_id) VALUES 
('Sintaxe', 'Estudo da disposição das palavras.', 'aprovado', 1, 3), 
('Equação', 'Sentença matemática.', 'pendente', 2, 4);