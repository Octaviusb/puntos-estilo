<?php
require_once 'config.php';

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Datos del administrador
$adminEmail = 'obuitragocamelo@yahoo.es';
$newPassword = 'Obc19447/*';

// Generar hash de la contraseña
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Actualizar la contraseña
$sql = "UPDATE usuarios SET contraseña = ? WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $adminEmail);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "✓ Contraseña actualizada exitosamente\n";
        echo "Nuevas credenciales:\n";
        echo "Email: " . $adminEmail . "\n";
        echo "Contraseña: " . $newPassword . "\n";
    } else {
        echo "No se encontró el usuario administrador\n";
    }
} else {
    echo "Error actualizando contraseña: " . $conn->error . "\n";
}

$stmt->close();
$conn->close();
?> 