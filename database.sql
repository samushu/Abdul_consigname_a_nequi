-- ============================================================
--  RumBoss · Gestor de Alquiler de Vehículos
--  Script de creación de base de datos MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS rumboss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rumboss;

-- ------------------------------------------------------------
-- Tabla: clientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clientes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    documento   VARCHAR(20)  NOT NULL UNIQUE,
    telefono    VARCHAR(20),
    email       VARCHAR(100),
    licencia    VARCHAR(30)  NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabla: vehiculos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vehiculos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    marca       VARCHAR(60)  NOT NULL,
    modelo      VARCHAR(60)  NOT NULL,
    anio        YEAR         NOT NULL,
    placa       VARCHAR(20)  NOT NULL UNIQUE,
    categoria   ENUM('AUTOMOVIL','CAMIONETA','MOTO') NOT NULL DEFAULT 'AUTOMOVIL',
    estado      ENUM('DISPONIBLE','ALQUILADO','MANTENIMIENTO') NOT NULL DEFAULT 'DISPONIBLE',
    precio_dia  DECIMAL(10,2) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabla: reservas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id      INT NOT NULL,
    vehiculo_id     INT NOT NULL,
    fecha_inicio    DATE NOT NULL,
    fecha_fin       DATE NOT NULL,
    total           DECIMAL(10,2),
    estado          ENUM('ACTIVA','FINALIZADA','CANCELADA') NOT NULL DEFAULT 'ACTIVA',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)  REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Datos de prueba
-- ------------------------------------------------------------
INSERT INTO clientes (nombre, documento, telefono, email, licencia) VALUES
('Carlos Rodríguez',  '1020304050', '3101234567', 'carlos@mail.com',   'LIC-001'),
('Laura Gómez',       '2030405060', '3209876543', 'laura@mail.com',    'LIC-002'),
('Andrés Martínez',   '3040506070', '3155551234', 'andres@mail.com',   'LIC-003');

INSERT INTO vehiculos (marca, modelo, anio, placa, categoria, estado, precio_dia) VALUES
('Toyota',    'Hilux 2024',    2024, 'ABC-123', 'CAMIONETA',  'DISPONIBLE', 250000),
('Mazda',     'CX-5',          2023, 'DEF-456', 'AUTOMOVIL',  'DISPONIBLE', 180000),
('Honda',     'CB500F',        2022, 'GHI-789', 'MOTO',       'DISPONIBLE',  80000),
('Chevrolet', 'Tracker',       2023, 'JKL-012', 'AUTOMOVIL',  'ALQUILADO',  160000),
('Ford',      'Ranger',        2024, 'MNO-345', 'CAMIONETA',  'MANTENIMIENTO', 220000);

-- ============================================================
--  RumBoss · Migración: agregar imagen a vehículos
--  Ejecutar UNA sola vez en tu gestor (phpMyAdmin / terminal)
-- ============================================================
ALTER TABLE vehiculos
    ADD COLUMN imagen VARCHAR(255) NULL DEFAULT NULL
    AFTER precio_dia;