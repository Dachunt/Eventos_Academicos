CREATE DATABASE IF NOT EXISTS eventos_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE eventos_db;

CREATE TABLE tipos_evento (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE eventos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    tipo_id INT UNSIGNED NOT NULL,
    fecha_evento DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    lugar VARCHAR(100) NOT NULL,
    cupo INT UNSIGNED NOT NULL,
    inscritos INT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT chk_eventos_cupo CHECK (cupo BETWEEN 1 AND 300),
    CONSTRAINT chk_eventos_inscritos CHECK (inscritos <= cupo),
    CONSTRAINT fk_eventos_tipo
        FOREIGN KEY (tipo_id) REFERENCES tipos_evento (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    INDEX idx_eventos_fecha (fecha_evento),
    INDEX idx_eventos_tipo (tipo_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipos_evento (nombre) VALUES
    ('Conferencia'),
    ('Taller'),
    ('Seminario'),
    ('Hackatón');

INSERT INTO eventos
    (titulo, descripcion, tipo_id, fecha_evento, hora_inicio, lugar, cupo, inscritos)
VALUES
    ('Conferencia de innovación educativa', 'Tendencias tecnológicas aplicadas a la educación superior.', 1, '2026-10-15', '09:00:00', 'Auditorio Principal', 150, 82),
    ('Taller de desarrollo web', 'Práctica guiada de PHP, bases de datos y APIs.', 2, '2026-11-05', '14:00:00', 'Laboratorio 2', 35, 30),
    ('Seminario de investigación', 'Buenas prácticas para formular proyectos de investigación.', 3, '2026-12-02', '10:00:00', 'Sala de Conferencias', 80, 44),
    ('Hackatón de soluciones sociales', 'Jornada colaborativa para crear prototipos con impacto local.', 4, '2026-10-24', '08:00:00', 'Centro de Innovación', 100, 97),
    ('Conferencia de ciclo anterior', 'Evento histórico conservado para probar restricciones de eliminación.', 1, '2026-08-20', '16:00:00', 'Auditorio Principal', 120, 120);