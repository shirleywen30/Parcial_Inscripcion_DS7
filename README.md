-- ============================================================
--  Base de datos: formulario_inscripcion
--  Proyecto: Formulario de Inscripción iTECH
-- ============================================================

CREATE DATABASE IF NOT EXISTS formulario_inscripcion
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE formulario_inscripcion;

-- ------------------------------------------------------------
--  Tablas de referencia (sin dependencias)
-- ------------------------------------------------------------

CREATE TABLE paises (
    id     INT          AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE nacionalidades (
    id     INT          AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE areas_interes (
    id     INT          AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Tabla principal de inscriptores
-- ------------------------------------------------------------

CREATE TABLE inscriptores (
    id                  INT           AUTO_INCREMENT PRIMARY KEY,
    identificacion      VARCHAR(20)   NOT NULL UNIQUE,
    nombre              VARCHAR(100)  NOT NULL,
    apellido            VARCHAR(100)  NOT NULL,
    edad                INT           NOT NULL,
    sexo                VARCHAR(20)   NOT NULL,
    pais_residencia_id  INT           NOT NULL,
    nacionalidad_id     INT           NOT NULL,
    correo              VARCHAR(150)  NOT NULL UNIQUE,
    celular             VARCHAR(20)   NOT NULL UNIQUE,
    observaciones       TEXT,
    firma               TEXT,
    fecha_registro      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_inscriptores_pais
        FOREIGN KEY (pais_residencia_id) REFERENCES paises(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_inscriptores_nacionalidad
        FOREIGN KEY (nacionalidad_id) REFERENCES nacionalidades(id)
        ON DELETE RESTRICT ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Tabla relacional: inscriptor <-> áreas de interés
-- ------------------------------------------------------------

CREATE TABLE inscriptor_temas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    inscriptor_id   INT NOT NULL,
    area_interes_id INT NOT NULL,

    CONSTRAINT fk_temas_inscriptor
        FOREIGN KEY (inscriptor_id) REFERENCES inscriptores(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_temas_area
        FOREIGN KEY (area_interes_id) REFERENCES areas_interes(id)
        ON DELETE RESTRICT ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Datos iniciales
-- ------------------------------------------------------------

INSERT INTO paises (nombre) VALUES
    ('Argentina'), ('Chile'), ('Colombia'), ('Costa Rica'),
    ('España'), ('Estados Unidos'), ('México'),
    ('Panamá'), ('Perú'), ('Venezuela');

INSERT INTO nacionalidades (nombre) VALUES
    ('Argentino/a'), ('Chileno/a'), ('Colombiano/a'), ('Costarricense'),
    ('Español/a'), ('Estadounidense'), ('Mexicano/a'),
    ('Panameño/a'), ('Peruano/a'), ('Venezolano/a');

INSERT INTO areas_interes (nombre) VALUES
    ('Cloud Computing'),
    ('Big Data'),
    ('Desarrollo Móvil'),
    ('Ciberseguridad'),
    ('IoT (Internet de las Cosas)'),
    ('Machine Learning'),
    ('DevOps'),
    ('Python');
