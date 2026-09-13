CREATE DATABASE IF NOT EXISTS gestion_nomina
  CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci;

USE gestion_nomina;

CREATE TABLE usuarios (
    id_usuario          INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo     VARCHAR(150) NOT NULL,
    correo              VARCHAR(150) NOT NULL UNIQUE,
    password_hash       VARCHAR(255) NOT NULL,
    token_recuperacion  VARCHAR(64)  NULL,
    token_expira        DATETIME     NULL,
    estado              ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    creado_en           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


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

-- devengados / deducciones / prestaciones sociales
CREATE TABLE conceptos_nomina (
    id_concepto     INT AUTO_INCREMENT PRIMARY KEY,
    nombre_concepto VARCHAR(150) NOT NULL,
    tipo            ENUM('DEVENGADO', 'DEDUCCION', 'PRESTACION_SOCIAL') NOT NULL,
    naturaleza      ENUM('FIJO', 'PORCENTAJE') NOT NULL DEFAULT 'FIJO',
    porcentaje      DECIMAL(6,4) NULL COMMENT 'Ej: 0.0833 para Prima, 0.04 para Salud',
    estado          ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO'
) ENGINE=InnoDB;


CREATE TABLE empleados (
    id_empleado           INT AUTO_INCREMENT PRIMARY KEY,
    numero_identificacion VARCHAR(20) NOT NULL UNIQUE,
    nombre_completo       VARCHAR(150) NOT NULL,
    id_cargo              INT NOT NULL,
    id_centro_costo       INT NOT NULL,
    sueldo_base           DECIMAL(12,2) NOT NULL,
    fecha_ingreso         DATE NOT NULL,
    estado                ENUM('ACTIVO', 'INACTIVO') NOT NULL DEFAULT 'ACTIVO',
    CONSTRAINT fk_empleado_cargo
        FOREIGN KEY (id_cargo) REFERENCES cargos(id_cargo),
    CONSTRAINT fk_empleado_centro_costo
        FOREIGN KEY (id_centro_costo) REFERENCES centros_costo(id_centro_costo)
) ENGINE=InnoDB;


CREATE TABLE nominas (
    id_nomina       INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio    DATE NOT NULL,
    fecha_fin       DATE NOT NULL,
    descripcion     VARCHAR(100) NOT NULL COMMENT 'Ej: Nómina 01 al 15 Enero 2026',
    estado          ENUM('BORRADOR', 'LIQUIDADA', 'ANULADA') NOT NULL DEFAULT 'BORRADOR',
    id_usuario_creo INT NOT NULL,
    creado_en       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_nomina_usuario
        FOREIGN KEY (id_usuario_creo) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB;

-- Detalle de nómina (entidad asociativa: empleado + nómina + concepto)

CREATE TABLE detalle_nomina (
    id_detalle    INT AUTO_INCREMENT PRIMARY KEY,
    id_nomina     INT NOT NULL,
    id_empleado   INT NOT NULL,
    id_concepto   INT NOT NULL,
    dias          DECIMAL(5,2) DEFAULT NULL COMMENT 'Días laborados/aplicados para este concepto',
    valor         DECIMAL(12,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_detalle_nomina
        FOREIGN KEY (id_nomina) REFERENCES nominas(id_nomina),
    CONSTRAINT fk_detalle_empleado
        FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
    CONSTRAINT fk_detalle_concepto
        FOREIGN KEY (id_concepto) REFERENCES conceptos_nomina(id_concepto),
    UNIQUE KEY uq_nomina_empleado_concepto (id_nomina, id_empleado, id_concepto)
) ENGINE=InnoDB;


CREATE TABLE prestamos (
    id_prestamo       INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado       INT NOT NULL,
    monto_desembolso  DECIMAL(12,2) NOT NULL,
    numero_cuotas     INT NOT NULL,
    fecha_desembolso  DATE NOT NULL,
    valor_cuota       DECIMAL(12,2) NOT NULL,
    cuotas_pagadas    INT NOT NULL DEFAULT 0,
    saldo_actual      DECIMAL(12,2) NOT NULL,
    estado            ENUM('ACTIVO', 'PAGADO') NOT NULL DEFAULT 'ACTIVO',
    CONSTRAINT fk_prestamo_empleado
        FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
) ENGINE=InnoDB;

-- ==========================================================
-- ADMINISTRADOR SEMILLA
-- ==========================================================
-- IMPORTANTE: password_hash NO se puede generar en SQL puro, hay que
-- generarlo con PHP y pegar aquí el resultado. En tu terminal ejecuta:
--
--   php -r "echo password_hash('TuClaveSegura123', PASSWORD_DEFAULT);"
--
-- Copia el resultado (empieza por $2y$...) y reemplaza el valor de abajo
-- antes de ejecutar este INSERT.

INSERT INTO usuarios (nombre_completo, correo, password_hash)
VALUES ('Administrador', 'admin@nomina.com', 'PEGA_AQUI_EL_HASH_GENERADO');
