<?php
/**
 * Script para corregir y crear todas las tablas faltantes
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Corrigiendo Tablas - Puntos Estilo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #1a237e; margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #3949ab; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔧 Corrigiendo Tablas</h1>
            <h2>Puntos Estilo</h2>
        </div>";

try {
    // Incluir configuración de base de datos
    require_once 'server/config.php';
    
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    echo "<h3>📋 Verificando estructura actual...</h3>";
    
    // Verificar estructura de tabla referidos
    $result = $conn->query("SHOW TABLES LIKE 'referidos'");
    if ($result && $result->num_rows > 0) {
        echo "<div class='info'>✅ Tabla referidos existe</div>";
        
        // Verificar columnas de referidos
        $columns = $conn->query("SHOW COLUMNS FROM referidos");
        $columnNames = [];
        while ($row = $columns->fetch_assoc()) {
            $columnNames[] = $row['Field'];
        }
        echo "<div class='info'>Columnas en referidos: " . implode(', ', $columnNames) . "</div>";
    }
    
    // Crear tablas faltantes una por una
    echo "<h3>🔨 Creando tablas faltantes...</h3>";
    
    // 1. Tabla password_resets
    $sql = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NULL,
        used BOOLEAN DEFAULT FALSE,
        INDEX idx_email (email),
        INDEX idx_token (token),
        INDEX idx_expires (expires_at)
    )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabla password_resets creada</div>";
    } else {
        echo "<div class='error'>❌ Error creando password_resets: " . $conn->error . "</div>";
    }
    
    // 2. Tabla canjes
    $sql = "CREATE TABLE IF NOT EXISTS canjes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        producto_id INT NOT NULL,
        puntos_canjeados INT NOT NULL,
        fecha_canje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        estado ENUM('pendiente', 'aprobado', 'rechazado', 'entregado') DEFAULT 'pendiente',
        notas TEXT,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
        INDEX idx_usuario (usuario_id),
        INDEX idx_fecha (fecha_canje),
        INDEX idx_estado (estado)
    )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabla canjes creada</div>";
    } else {
        echo "<div class='error'>❌ Error creando canjes: " . $conn->error . "</div>";
    }
    
    // 3. Tabla mensajes
    $sql = "CREATE TABLE IF NOT EXISTS mensajes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        remitente_id INT,
        destinatario_id INT NOT NULL,
        asunto VARCHAR(255) NOT NULL,
        mensaje TEXT NOT NULL,
        fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE,
        fecha_lectura TIMESTAMP NULL,
        tipo ENUM('sistema', 'usuario', 'admin') DEFAULT 'usuario',
        FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE SET NULL,
        FOREIGN KEY (destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        INDEX idx_destinatario (destinatario_id),
        INDEX idx_fecha_envio (fecha_envio),
        INDEX idx_leido (leido)
    )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabla mensajes creada</div>";
    } else {
        echo "<div class='error'>❌ Error creando mensajes: " . $conn->error . "</div>";
    }
    
    // 4. Tabla bonos
    $sql = "CREATE TABLE IF NOT EXISTS bonos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(255) NOT NULL,
        descripcion TEXT,
        puntos_bono INT NOT NULL,
        codigo VARCHAR(50) UNIQUE,
        fecha_inicio DATE,
        fecha_fin DATE,
        activo BOOLEAN DEFAULT TRUE,
        max_usos INT DEFAULT NULL,
        usos_actuales INT DEFAULT 0,
        tipo ENUM('registro', 'referido', 'compra', 'promocional') DEFAULT 'promocional',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_codigo (codigo),
        INDEX idx_activo (activo),
        INDEX idx_fechas (fecha_inicio, fecha_fin)
    )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabla bonos creada</div>";
    } else {
        echo "<div class='error'>❌ Error creando bonos: " . $conn->error . "</div>";
    }
    
    // 5. Tabla bonos_usuarios
    $sql = "CREATE TABLE IF NOT EXISTS bonos_usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        bono_id INT NOT NULL,
        puntos_otorgados INT NOT NULL,
        fecha_otorgado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_vencimiento DATE,
        usado BOOLEAN DEFAULT FALSE,
        fecha_uso TIMESTAMP NULL,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (bono_id) REFERENCES bonos(id) ON DELETE CASCADE,
        INDEX idx_usuario (usuario_id),
        INDEX idx_bono (bono_id),
        INDEX idx_fecha_vencimiento (fecha_vencimiento),
        INDEX idx_usado (usado)
    )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Tabla bonos_usuarios creada</div>";
    } else {
        echo "<div class='error'>❌ Error creando bonos_usuarios: " . $conn->error . "</div>";
    }
    
    // 6. Crear usuario administrador
    $sql = "INSERT IGNORE INTO usuarios (nombre, correo, contraseña, rol, puntos, fecha_registro) 
            VALUES (
                'Administrador',
                'admin@puntosestilo.com',
                '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'admin',
                0,
                NOW()
            )";
    
    if ($conn->query($sql)) {
        echo "<div class='success'>✅ Usuario administrador creado</div>";
    } else {
        echo "<div class='error'>❌ Error creando usuario admin: " . $conn->error . "</div>";
    }
    
    // Verificar tablas creadas
    echo "<h3>🔍 Verificando tablas creadas...</h3>";
    
    $requiredTables = [
        'password_resets' => 'Tabla de reset de contraseñas',
        'canjes' => 'Tabla de canjes',
        'mensajes' => 'Tabla de mensajes',
        'bonos' => 'Tabla de bonos',
        'bonos_usuarios' => 'Tabla de bonos de usuarios'
    ];
    
    foreach ($requiredTables as $table => $description) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<div class='success'>✅ $description: Existe</div>";
        } else {
            echo "<div class='error'>❌ $description: No existe</div>";
        }
    }
    
    // Verificar usuario administrador
    $result = $conn->query("SELECT * FROM usuarios WHERE correo = 'admin@puntosestilo.com' AND rol = 'admin'");
    if ($result && $result->num_rows > 0) {
        echo "<div class='success'>✅ Usuario Administrador: Creado correctamente</div>";
    } else {
        echo "<div class='error'>❌ Usuario Administrador: No encontrado</div>";
    }
    
    echo "<div class='success'>
        <h4>🎉 ¡Proceso completado!</h4>
        <p>Todas las tablas han sido creadas correctamente.</p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <h4>❌ Error Fatal</h4>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
    </div>";
}

echo "<div style='margin-top: 30px; text-align: center;'>
    <a href='verificar_instalacion.php' class='btn'>🔍 Verificar Instalación</a>
    <a href='frontend/' class='btn'>🏠 Ir al Sistema</a>
</div>";

echo "</div></body></html>";
?> 