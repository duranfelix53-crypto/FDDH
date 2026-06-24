CREATE DATABASE IF NOT EXISTS tienda_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tienda_mvc;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_compra DECIMAL(10,2) NOT NULL DEFAULT 0,
    precio_venta DECIMAL(10,2) NOT NULL DEFAULT 0,
    existencia INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) DEFAULT NULL,
    UNIQUE KEY uk_sku (sku)
) ENGINE=InnoDB;