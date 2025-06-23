<?php
/**
 * Script para corregir tablas faltantes
 * Verifica y crea las tablas necesarias del sistema
 */

require_once 'server/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Corrección de Tablas - Puntos Estilo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #1a237e; margin-bottom: 30px; }
        .message { padding: 15px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #3949ab; }
        .table-list { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 15px 0; }
        .table-item { margin: 5px 0; padding: 5px; border-radius: 3px; }
        .table-exists { background: #d4edda; color: #155724; }
        .table-missing { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔧 Corrección de Tablas</h1>
            <h2>Puntos Estilo</h2>
        </div>";

// Verificar conexión a base de datos
if ($conn->connect_error) {
    echo "<div class='message error'>
        <strong>Error de conexión:</strong> " . $conn->connect_error . "
    </div>";
    exit();
}

echo "<div class='message info'>
    <strong>✅ Conexión exitosa</strong> a la base de datos
</div>";

// Lista de tablas requeridas
$requiredTables = [
    'usuarios' => "
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
        )
    ",
    'password_resets' => "
        CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL,
            token VARCHAR(64) UNIQUE NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            used BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_token (token),
            INDEX idx_email (email)
        )
    ",
    'productos' => "
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
        )
    ",
    'transacciones' => "
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
        )
    ",
    'canjes' => "
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
        )
    ",
    'referidos' => "
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
        )
    ",
    'mensajes' => "
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
        )
    ",
    'aliados' => "
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
        )
    ",
    'bonos' => "
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
        )
    ",
    'bonos_usuarios' => "
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
        )
    "
];

$existingTables = [];
$missingTables = [];
$createdTables = [];

echo "<div class='table-list'>
    <h3>📋 Verificación de Tablas</h3>";

// Verificar cada tabla
foreach ($requiredTables as $tableName => $createSQL) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    
    if ($result->num_rows > 0) {
        $existingTables[] = $tableName;
        echo "<div class='table-item table-exists'>✅ $tableName - Existe</div>";
    } else {
        $missingTables[] = $tableName;
        echo "<div class='table-item table-missing'>❌ $tableName - Falta</div>";
    }
}

echo "</div>";

// Crear tablas faltantes
if (!empty($missingTables)) {
    echo "<div class='message warning'>
        <strong>⚠️ Se encontraron " . count($missingTables) . " tablas faltantes</strong><br>
        Creando tablas faltantes...
    </div>";
    
    foreach ($missingTables as $tableName) {
        $createSQL = $requiredTables[$tableName];
        
        // Dividir el SQL en múltiples consultas si es necesario
        $queries = explode(';', $createSQL);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if ($conn->query($query)) {
                    echo "<div class='message success'>✅ Tabla '$tableName' creada correctamente</div>";
                    $createdTables[] = $tableName;
                    break;
                } else {
                    echo "<div class='message error'>❌ Error al crear tabla '$tableName': " . $conn->error . "</div>";
                }
            }
        }
    }
} else {
    echo "<div class='message success'>
        <strong>✅ Todas las tablas existen</strong>
    </div>";
}

// Crear índices adicionales
echo "<div class='message info'>
    <strong>🔧 Creando índices adicionales...</strong>
</div>";

$additionalIndexes = [
    "CREATE INDEX IF NOT EXISTS idx_usuarios_puntos ON usuarios(puntos)",
    "CREATE INDEX IF NOT EXISTS idx_productos_activo ON productos(activo)",
    "CREATE INDEX IF NOT EXISTS idx_canjes_estado ON canjes(estado)",
    "CREATE INDEX IF NOT EXISTS idx_transacciones_fecha ON transacciones(fecha)"
];

foreach ($additionalIndexes as $indexSQL) {
    if ($conn->query($indexSQL)) {
        echo "<div class='message success'>✅ Índice creado correctamente</div>";
    } else {
        echo "<div class='message warning'>⚠️ Índice ya existe o error: " . $conn->error . "</div>";
    }
}

// Insertar datos de ejemplo
echo "<div class='message info'>
    <strong>📝 Insertando datos de ejemplo...</strong>
</div>";

// Insertar usuario administrador
$adminEmail = 'obuitragocamelo@yahoo.es';
$adminPassword = password_hash('Admin123', PASSWORD_DEFAULT);

$checkAdmin = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
$checkAdmin->bind_param("s", $adminEmail);
$checkAdmin->execute();
$adminResult = $checkAdmin->get_result();

if ($adminResult->num_rows === 0) {
    $insertAdmin = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol, puntos) VALUES (?, ?, ?, ?, ?)");
    $nombre = 'Administrador';
    $rol = 'admin';
    $puntos = 0;
    $insertAdmin->bind_param("ssssi", $nombre, $adminEmail, $adminPassword, $rol, $puntos);
    
    if ($insertAdmin->execute()) {
        echo "<div class='message success'>✅ Usuario administrador creado</div>";
    } else {
        echo "<div class='message error'>❌ Error al crear usuario administrador: " . $insertAdmin->error . "</div>";
    }
} else {
    echo "<div class='message info'>✅ Usuario administrador ya existe</div>";
}

// Insertar productos de ejemplo
$sampleProducts = [
    ['Descuento 10% en tienda', 'Descuento del 10% en cualquier compra', 100, 1],
    ['Café gratis', 'Café americano gratis', 50, 1],
    ['Envío gratis', 'Envío gratis en tu próxima compra', 75, 0]
];

foreach ($sampleProducts as $product) {
    $checkProduct = $conn->prepare("SELECT id FROM productos WHERE nombre = ?");
    $checkProduct->bind_param("s", $product[0]);
    $checkProduct->execute();
    $productResult = $checkProduct->get_result();
    
    if ($productResult->num_rows === 0) {
        $insertProduct = $conn->prepare("INSERT INTO productos (nombre, descripcion, puntos, destacado) VALUES (?, ?, ?, ?)");
        $insertProduct->bind_param("ssii", $product[0], $product[1], $product[2], $product[3]);
        
        if ($insertProduct->execute()) {
            echo "<div class='message success'>✅ Producto '{$product[0]}' creado</div>";
        } else {
            echo "<div class='message error'>❌ Error al crear producto: " . $insertProduct->error . "</div>";
        }
    }
}

// Resumen final
echo "<div class='message success'>
    <h3>🎉 Corrección Completada</h3>
    <p><strong>Tablas existentes:</strong> " . count($existingTables) . "</p>
    <p><strong>Tablas creadas:</strong> " . count($createdTables) . "</p>
    <p><strong>Total de tablas:</strong> " . count($requiredTables) . "</p>
</div>";

// Credenciales
echo "<div class='message info'>
    <h3>🔑 Credenciales de Acceso</h3>
    <p><strong>Email:</strong> obuitragocamelo@yahoo.es</p>
    <p><strong>Contraseña:</strong> Admin123</p>
    <p><strong>Rol:</strong> Administrador</p>
</div>";

// Enlaces útiles
echo "<div style='text-align: center; margin-top: 30px;'>
    <a href='frontend/login_simple.php' class='btn'>Ir al Login Simple</a>
    <a href='frontend/login.php' class='btn'>Ir al Login con OTP</a>
    <a href='verificar_instalacion.php' class='btn'>Verificar Instalación</a>
    <a href='crear_admin.php' class='btn'>Crear Administrador</a>
</div>";

echo "</div></body></html>";
?> 