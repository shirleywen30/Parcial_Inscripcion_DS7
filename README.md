BASE DE DATOS UTILIZADO

CREATE DATABASE IF NOT EXISTS parcial_itech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE formulario_inscripcion;

CREATE TABLE paises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE nacionalidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE areas_interes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inscriptores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identificacion VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    sexo VARCHAR(20) NOT NULL,
    pais_residencia_id INT NOT NULL,
    nacionalidad_id INT NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    celular VARCHAR(20) NOT NULL UNIQUE,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pais_residencia_id) REFERENCES paises(id),
    FOREIGN KEY (nacionalidad_id) REFERENCES nacionalidades(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inscriptor_temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inscriptor_id INT NOT NULL,
    area_interes_id INT NOT NULL,
    FOREIGN KEY (inscriptor_id) REFERENCES inscriptores(id) ON DELETE CASCADE,
    FOREIGN KEY (area_interes_id) REFERENCES areas_interes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO paises (nombre) VALUES 
('Panamá'),
('Colombia'),
('Costa Rica'),
('México'),
('Estados Unidos'),
('España'),
('Argentina'),
('Chile'),
('Perú'),
('Venezuela');

INSERT INTO nacionalidades (nombre) VALUES 
('Panameño/a'),
('Colombiano/a'),
('Costarricense'),
('Mexicano/a'),
('Estadounidense'),
('Español/a'),
('Argentino/a'),
('Chileno/a'),
('Peruano/a'),
('Venezolano/a');

INSERT INTO areas_interes (nombre) VALUES 
('Desarrollo Web'),
('Inteligencia Artificial'),
('Ciberseguridad'),
('Desarrollo Móvil'),
('Cloud Computing'),
('Big Data'),
('IoT (Internet de las Cosas)'),
('Blockchain'),
('DevOps'),
('Machine Learning');
