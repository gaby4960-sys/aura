
CREATE DATABASE IF NOT EXISTS aura
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE aura;

-- TABELA DE USUARIOS
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente'
);

-- TABELA DE CATEGORIAS
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

-- TABELA DE PRODUTOS
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- TABELA DE PEDIDOS
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    forma_pagamento ENUM('PIX', 'Boleto', 'Cartão') NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- TABELA DE ITENS DO PEDIDO
CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- CATEGORIAS
INSERT INTO categorias (nome) VALUES
('Florais'),
('Doces'),
('Frescas'),
('Especiais');

-- 16 VELAS AROMATICAS
INSERT INTO produtos
(categoria_id, nome, descricao, preco, estoque, imagem)
VALUES

-- FLORAIS
(1, 'Vela de Lavanda',
 'Fragrancia floral suave e delicada.',
 49.90, 10, 'lavanda.jpg'),

(1, 'Vela de Rosas',
 'Aroma romantico e delicado de rosas.',
 54.90, 10, 'rosas.jpg'),

(1, 'Vela de Jasmim',
 'Fragrancia floral elegante e envolvente.',
 49.90, 8, 'jasmim.jpg'),

(1, 'Vela de Flor de Cerejeira',
 'Aroma floral leve e levemente adocicado.',
 59.90, 8, 'cerejeira.jpg'),

-- DOCES
(2, 'Vela de Baunilha',
 'Aroma doce, suave e aconchegante.',
 44.90, 12, 'baunilha.jpg'),

(2, 'Vela de Caramelo',
 'Fragrancia doce e acolhedora.',
 49.90, 10, 'caramelo.jpg'),

(2, 'Vela de Chocolate',
 'Aroma intenso e adocicado de chocolate.',
 54.90, 8, 'chocolate.jpg'),

(2, 'Vela de Coco',
 'Fragrancia tropical e cremosa.',
 44.90, 10, 'coco.jpg'),

-- FRESCAS
(3, 'Vela de Capim-Limao',
 'Aroma citrico, leve e refrescante.',
 44.90, 10, 'capim-limao.jpg'),

(3, 'Vela de Eucalipto',
 'Fragrancia herbal e refrescante.',
 49.90, 8, 'eucalipto.jpg'),

(3, 'Vela de Hortela',
 'Aroma fresco e delicadamente herbal.',
 44.90, 10, 'hortela.jpg'),

(3, 'Vela de Cha Branco',
 'Fragrancia suave, fresca e elegante.',
 59.90, 8, 'cha-branco.jpg'),

-- ESPECIAIS
(4, 'Vela de Sandalo',
 'Aroma amadeirado e sofisticado.',
 64.90, 6, 'sandalo.jpg'),

(4, 'Vela de Ambar',
 'Fragrancia quente e envolvente.',
 69.90, 6, 'ambar.jpg'),

(4, 'Vela de Canela',
 'Aroma marcante e aconchegante.',
 49.90, 10, 'canela.jpg'),

(4, 'Vela de Madeira e Especiarias',
 'Combinacao de notas amadeiradas e especiarias.',
 74.90, 5, 'madeira.jpg');
