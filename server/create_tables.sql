-- Script para crear las tablas necesarias del sistema Puntos Estilo

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    puntos INT DEFAULT 0,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    estado BOOLEAN DEFAULT TRUE,
    avatar VARCHAR(255),
    INDEX idx_correo (correo),
    INDEX idx_rol (rol)
);

-- Tabla para reset de contraseñas
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- Tabla de productos/beneficios
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    puntos INT NOT NULL,
    imagen VARCHAR(255),
    stock INT DEFAULT 0,
    destacado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_puntos (puntos),
    INDEX idx_destacado (destacado)
);

-- Tabla de transacciones
CREATE TABLE IF NOT EXISTS transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('acumulacion', 'canje', 'bonificacion', 'descuento') NOT NULL,
    puntos INT NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referencia VARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha),
    INDEX idx_tipo (tipo)
);

-- Tabla de canjes
CREATE TABLE IF NOT EXISTS canjes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    puntos_gastados INT NOT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado', 'entregado') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_procesamiento TIMESTAMP NULL,
    comentarios TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_usuario_estado (usuario_id, estado),
    INDEX idx_fecha_solicitud (fecha_solicitud)
);

-- Tabla de referidos
CREATE TABLE IF NOT EXISTS referidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_referidor_id INT NOT NULL,
    usuario_referido_id INT NOT NULL,
    fecha_referido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    puntos_bonificacion INT DEFAULT 0,
    estado ENUM('pendiente', 'activo', 'inactivo') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_referidor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_referido_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_referido (usuario_referido_id),
    INDEX idx_referidor (usuario_referidor_id)
);

-- Tabla de mensajes
CREATE TABLE IF NOT EXISTS mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_leido (usuario_id, leido),
    INDEX idx_fecha_envio (fecha_envio)
);

-- Tabla de aliados comerciales
CREATE TABLE IF NOT EXISTS aliados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    logo VARCHAR(255),
    sitio_web VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de bonos/promociones
CREATE TABLE IF NOT EXISTS bonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    puntos INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fechas (fecha_inicio, fecha_fin)
);

-- Tabla de asignación de bonos a usuarios
CREATE TABLE IF NOT EXISTS bonos_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bono_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilizado BOOLEAN DEFAULT FALSE,
    fecha_uso TIMESTAMP NULL,
    FOREIGN KEY (bono_id) REFERENCES bonos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_utilizado (usuario_id, utilizado)
);

-- Insertar usuario administrador por defecto con las credenciales correctas
INSERT INTO usuarios (nombre, correo, contraseña, rol, puntos) VALUES 
('Administrador', 'obuitragocamelo@yahoo.es', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0)
ON DUPLICATE KEY UPDATE id=id;

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, descripcion, puntos, destacado) VALUES 
('Descuento 10% en tienda', 'Descuento del 10% en cualquier compra', 100, TRUE),
('Café gratis', 'Café americano gratis', 50, TRUE),
('Envío gratis', 'Envío gratis en tu próxima compra', 75, FALSE)
ON DUPLICATE KEY UPDATE id=id;

-- Crear índices adicionales para optimización
CREATE INDEX idx_usuarios_puntos ON usuarios(puntos);
CREATE INDEX idx_productos_activo ON productos(activo);
CREATE INDEX idx_canjes_estado ON canjes(estado);
CREATE INDEX idx_transacciones_fecha ON transacciones(fecha); 