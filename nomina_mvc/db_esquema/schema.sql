CREATE DATABASE IF NOT EXISTS gestion_nomina
  CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci;

USE gestion_nomina;

CREATE TABLE cargos (
    id_cargo      INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cargo  VARCHAR(100) NOT NULL,
    estado        ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB;

CREATE TABLE centros_costo (
    id_centro_costo INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,
    estado          ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB;

CREATE TABLE conceptos_nomina (
    id_concepto     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_concepto VARCHAR(150) NOT NULL,
    tipo            ENUM('DEVENGADO', 'DEDUCCION', 'PRESTACION_SOCIAL') NOT NULL,
    naturaleza      ENUM('FIJO', 'PORCENTAJE') NOT NULL DEFAULT 'FIJO',
    porcentaje      DECIMAL(6,4) NULL COMMENT 'Ej: 0.0833 para Prima, 0.04 para Salud',
    estado          ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB;

-- =================================================================
-- 1. TABLA MAESTRA UNIFICADA (Reemplaza a usuarios + empleados)
-- =================================================================
CREATE TABLE usuarios (
    id_usuario            INT AUTO_INCREMENT PRIMARY KEY,
    numero_identificacion VARCHAR(20)  NULL UNIQUE COMMENT 'Cédula (Opcional para el Admin)',
    nombre_completo       VARCHAR(150) NOT NULL,
    correo                VARCHAR(150) NOT NULL UNIQUE,
    telefono              VARCHAR(20)  NULL,
    password_hash         VARCHAR(255) NOT NULL,
    rol                   ENUM('ADMIN', 'EMPLEADO') NOT NULL DEFAULT 'EMPLEADO',
    id_cargo              INT          NULL COMMENT 'NULL si es administrador',
    id_centro_costo       INT          NULL COMMENT 'NULL si es administrador',
    sueldo_base           DECIMAL(12,2) NULL,
    fecha_ingreso         DATE         NULL,
    token_recuperacion    VARCHAR(64)  NULL,
    token_expira          DATETIME     NULL,
    estado                ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    creado_en             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_cargo
        FOREIGN KEY (id_cargo) REFERENCES cargos(id_cargo),
    CONSTRAINT fk_usuario_centro_costo
        FOREIGN KEY (id_centro_costo) REFERENCES centros_costo(id_centro_costo)
) ENGINE=InnoDB;

CREATE TABLE nominas (
    id_nomina       INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio    DATE NOT NULL,
    fecha_fin       DATE NOT NULL,
    descripcion     VARCHAR(100) NOT NULL,
    estado          ENUM('BORRADOR', 'LIQUIDADA', 'ANULADA') NOT NULL DEFAULT 'BORRADOR',
    id_usuario_creo INT NOT NULL,
    creado_en       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_nomina_usuario
        FOREIGN KEY (id_usuario_creo) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- =================================================================
-- 2. DETALLE NÓMINA (Ahora apunta al id_usuario)
-- =================================================================
CREATE TABLE detalle_nomina (
    id_detalle    INT AUTO_INCREMENT PRIMARY KEY,
    id_nomina     INT NOT NULL,
    id_usuario    INT NOT NULL COMMENT 'Reemplaza a id_empleado',
    id_concepto   INT NOT NULL,
    dias          DECIMAL(5,2) DEFAULT NULL,
    valor         DECIMAL(12,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_detalle_nomina
        FOREIGN KEY (id_nomina) REFERENCES nominas(id_nomina),
    CONSTRAINT fk_detalle_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    CONSTRAINT fk_detalle_concepto
        FOREIGN KEY (id_concepto) REFERENCES conceptos_nomina(id_concepto),
    UNIQUE KEY uq_nomina_usuario_concepto (id_nomina, id_usuario, id_concepto)
) ENGINE=InnoDB;

-- =================================================================
-- 3. PRÉSTAMOS (Ahora apunta al id_usuario)
-- =================================================================
CREATE TABLE prestamos (
    id_prestamo       INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL COMMENT 'Reemplaza a id_empleado',
    monto_desembolso  DECIMAL(12,2) NOT NULL,
    numero_cuotas     INT NOT NULL,
    fecha_desembolso  DATE NOT NULL,
    valor_cuota       DECIMAL(12,2) NOT NULL,
    cuotas_pagadas    INT NOT NULL DEFAULT 0,
    saldo_actual      DECIMAL(12,2) NOT NULL,
    estado            ENUM('ACTIVO', 'PAGADO') NOT NULL DEFAULT 'ACTIVO',
    CONSTRAINT fk_prestamo_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- ==========================================================
-- ADMINISTRADOR SEMILLA (Ahora con ROL)
-- ==========================================================
-- Reemplaza 'PEGA_AQUI_EL_HASH_GENERADO' por el hash de tu clave "123456" o "admin"
INSERT INTO usuarios (numero_identificacion, nombre_completo, correo, telefono, password_hash, rol)
VALUES ('000000000', 'Administrador Principal', 'admin@nomina.com', '3000000000', 'PEGA_AQUI_EL_HASH_GENERADO', 'ADMIN');

-- Insertar Cargos básicos
INSERT INTO cargos (id_cargo, nombre_cargo, estado) VALUES
(1, 'Gerente', 'ACTIVO'),
(2, 'Asistente', 'ACTIVO'),
(3, 'Vendedor', 'ACTIVO')
ON DUPLICATE KEY UPDATE nombre_cargo = VALUES(nombre_cargo);

-- Insertar Centros de Costo básicos
INSERT INTO centros_costo (id_centro_costo, nombre, estado) VALUES
(1, 'Administración', 'ACTIVO'),
(2, 'Operaciones', 'ACTIVO'),
(3, 'Ventas', 'ACTIVO')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Insertar Conceptos de Nómina iniciales
INSERT INTO conceptos_nomina (id_concepto, nombre_concepto, tipo, naturaleza, porcentaje, estado) VALUES
(1, 'Sueldo Básico', 'DEVENGADO', 'FIJO', NULL, 'ACTIVO'),
(2, 'Incapacidad EPS', 'DEVENGADO', 'PORCENTAJE', 0.6667, 'ACTIVO'),
(3, 'Incapacidad ARL', 'DEVENGADO', 'PORCENTAJE', 1.0000, 'ACTIVO'),
(4, 'Recargo Nocturno', 'DEVENGADO', 'PORCENTAJE', 0.3500, 'ACTIVO'),
(5, 'Horas Dominicales', 'DEVENGADO', 'PORCENTAJE', 1.7500, 'ACTIVO'),
(6, 'Auxilio de Transporte', 'DEVENGADO', 'FIJO', NULL, 'ACTIVO'),
(7, 'Salud Empleado', 'DEDUCCION', 'PORCENTAJE', 0.0400, 'ACTIVO'),
(8, 'Pensión Empleado', 'DEDUCCION', 'PORCENTAJE', 0.0400, 'ACTIVO'),
(9, 'Fondo de Solidaridad', 'DEDUCCION', 'PORCENTAJE', 0.0100, 'ACTIVO'),
(10, 'Cuota Préstamo', 'DEDUCCION', 'FIJO', NULL, 'ACTIVO')
ON DUPLICATE KEY UPDATE nombre_concepto = VALUES(nombre_concepto);

