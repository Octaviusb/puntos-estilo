<?php
require_once 'config.php';

echo "<h2>Creando tablas adicionales</h2>";

// 1. Tabla de notificaciones
$sql = "CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('vencimiento', 'carga', 'descuento', 'canje', 'general') NOT NULL,
    descripcion TEXT,
    leida BOOLEAN DEFAULT FALSE,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla notificaciones creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla notificaciones: " . $conn->error . "<br>";
}

// 2. Tabla de reportes diarios
$sql = "CREATE TABLE IF NOT EXISTS reportes_diarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    tipo ENUM('diario', 'semanal', 'mensual') NOT NULL,
    contenido TEXT,
    generado_por INT,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generado_por) REFERENCES usuarios(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla reportes_diarios creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla reportes_diarios: " . $conn->error . "<br>";
}

// 3. Tabla de configuración del sistema
$sql = "CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla configuracion_sistema creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla configuracion_sistema: " . $conn->error . "<br>";
}

// 4. Insertar configuraciones por defecto
$configuraciones = [
    ['email_notificaciones', 'true', 'Habilitar notificaciones por email'],
    ['dias_vencimiento_puntos', '365', 'Días antes del vencimiento para notificar'],
    ['stock_minimo_alerta', '5', 'Stock mínimo para generar alertas'],
    ['puntos_por_registro', '100', 'Puntos otorgados al registrarse'],
    ['max_puntos_usuario', '100000', 'Máximo de puntos por usuario']
];

foreach ($configuraciones as $config) {
    $sql = "INSERT IGNORE INTO configuracion_sistema (clave, valor, descripcion) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $config[0], $config[1], $config[2]);
    $stmt->execute();
}

echo "✓ Configuraciones por defecto insertadas<br>";

// 5. Tabla de logs del sistema
$sql = "CREATE TABLE IF NOT EXISTS logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nivel ENUM('INFO', 'WARNING', 'ERROR', 'DEBUG') NOT NULL,
    mensaje TEXT NOT NULL,
    usuario_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla logs_sistema creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla logs_sistema: " . $conn->error . "<br>";
}

// 6. Tabla de sesiones activas
$sql = "CREATE TABLE IF NOT EXISTS sesiones_activas (
    id VARCHAR(128) PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    datos_sesion TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla sesiones_activas creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla sesiones_activas: " . $conn->error . "<br>";
}

$conn->close();
echo "<br><strong>¡Proceso completado!</strong><br>";
echo "<a href='../frontend/administracion.php' style='color: blue; text-decoration: none;'>→ Ir al Panel de Administración</a>";
?> 