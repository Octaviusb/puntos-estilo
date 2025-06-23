<?php
/**
 * Script para crear/verificar usuario administrador
 * Ejecutar una sola vez para configurar el sistema
 */

require_once 'server/config.php';

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Crear Administrador - Puntos Estilo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #1a237e; margin-bottom: 30px; }
        .message { padding: 15px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #3949ab; }
        .credentials { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔧 Configuración de Administrador</h1>
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

// Verificar si la tabla usuarios existe
$result = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($result->num_rows === 0) {
    echo "<div class='message error'>
        <strong>❌ Error:</strong> La tabla 'usuarios' no existe.<br>
        Ejecuta primero el script <code>server/create_tables.sql</code>
    </div>";
    exit();
}

echo "<div class='message success'>
    <strong>✅ Tabla usuarios encontrada</strong>
</div>";

// Verificar si el usuario administrador existe
$email = 'obuitragocamelo@yahoo.es';
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<div class='message success'>
        <strong>✅ Usuario administrador ya existe</strong><br>
        ID: " . $user['id'] . "<br>
        Nombre: " . htmlspecialchars($user['nombre']) . "<br>
        Email: " . htmlspecialchars($user['correo']) . "<br>
        Rol: " . htmlspecialchars($user['rol']) . "
    </div>";
    
    // Verificar si la contraseña es la correcta
    if (password_verify('Admin123', $user['contraseña'])) {
        echo "<div class='message info'>
            <strong>✅ Contraseña correcta</strong>
        </div>";
    } else {
        echo "<div class='message error'>
            <strong>⚠️ La contraseña no coincide</strong><br>
            Actualizando contraseña...
        </div>";
        
        // Actualizar contraseña
        $hashedPassword = password_hash('Admin123', PASSWORD_DEFAULT);
        $updateSql = "UPDATE usuarios SET contraseña = ? WHERE correo = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ss", $hashedPassword, $email);
        
        if ($updateStmt->execute()) {
            echo "<div class='message success'>
                <strong>✅ Contraseña actualizada correctamente</strong>
            </div>";
        } else {
            echo "<div class='message error'>
                <strong>❌ Error al actualizar contraseña:</strong> " . $updateStmt->error . "
            </div>";
        }
    }
    
} else {
    // Crear usuario administrador con las credenciales especificadas
    $nombre = 'Administrador';
    $password = 'Admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $rol = 'admin';
    $puntos = 0;
    
    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol, puntos) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $email, $hashedPassword, $rol, $puntos);
    
    if ($stmt->execute()) {
        echo "<div class='message success'>
            <strong>✅ Usuario administrador creado exitosamente</strong>
        </div>";
    } else {
        echo "<div class='message error'>
            <strong>❌ Error al crear usuario:</strong> " . $stmt->error . "
        </div>";
        exit();
    }
}

// Mostrar credenciales
echo "<div class='credentials'>
    <h3>🔑 Credenciales de Acceso</h3>
    <p><strong>Email:</strong> obuitragocamelo@yahoo.es</p>
    <p><strong>Contraseña:</strong> Admin123</p>
    <p><strong>Rol:</strong> Administrador</p>
    <p><em>⚠️ IMPORTANTE: Cambia la contraseña después del primer login</em></p>
</div>";

// Verificar otros usuarios de ejemplo
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$total = $result->fetch_assoc()['total'];

echo "<div class='message info'>
    <strong>📊 Total de usuarios en el sistema:</strong> $total
</div>";

// Enlaces útiles
echo "<div style='text-align: center; margin-top: 30px;'>
    <a href='frontend/login_simple.php' class='btn'>Ir al Login Simple</a>
    <a href='frontend/login.php' class='btn'>Ir al Login con OTP</a>
    <a href='verificar_instalacion.php' class='btn'>Verificar Instalación</a>
</div>";

echo "</div></body></html>";
?> 