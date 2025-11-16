-- Crear base de datos
CREATE DATABASE IF NOT EXISTS halloween;
USE halloween;

-- Tabla disfraces
CREATE TABLE disfraces (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    votos INT(11) NOT NULL DEFAULT 0,
    foto VARCHAR(100) NOT NULL,
    foto_blob LONGBLOB,
    eliminado INT(11) NOT NULL DEFAULT 0
);

-- Tabla usuarios
CREATE TABLE usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    clave TEXT NOT NULL,
    es_admin INT(11) NOT NULL DEFAULT 0
);

-- Tabla votos
CREATE TABLE votos (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT(11) NOT NULL,
    id_disfraz INT(11) NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_voto (id_usuario, id_disfraz)
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, clave, es_admin) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insertar algunos disfraces de ejemplo
INSERT INTO disfraces (nombre, descripcion, foto) VALUES 
('Vampiro', 'Disfraz clásico de vampiro con capa y colmillos', 'vampiro.jpg'),
('Bruja', 'Tradicional disfraz de bruja con sombrero puntiagudo', 'bruja.jpg'),
('Fantasma', 'Sábana blanca con agujeros para los ojos', 'fantasma.jpg'),
('Calabaza', 'Disfraz de jack-o-lantern naranja', 'calabaza.jpg');
