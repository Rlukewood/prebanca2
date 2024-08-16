CREATE DATABASE `usuarios_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;


CREATE DATABASE `usuarios_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;



-- Criação do banco de dados
CREATE DATABASE usuarios_db;

-- Seleção do banco de dados
USE usuarios_db;

-- Criação da tabela de usuários
CREATE TABLE usuarios (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    cpf_cnpj VARCHAR(18) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(15) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Seleciona o banco de dados
USE usuarios_db;

-- Tabela para armazenar informações dos usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Tabela para armazenar imagens para o carrossel
CREATE TABLE IF NOT EXISTS imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagem LONGBLOB NOT NULL
);

