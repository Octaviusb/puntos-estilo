<?php
// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Contraseña vacía por defecto en XAMPP

// Crear conexión sin seleccionar base de datos
$conn = new mysqli($db_host, $db_user, $db_pass);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear la base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS mi_proyecto";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos creada o ya existente<br>";
} else {
    echo "Error creando la base de datos: " . $conn->error . "<br>";
}

// Seleccionar la base de datos
$conn->select_db('mi_proyecto');

// Crear tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    puntos INT DEFAULT 0,
    avatar VARCHAR(255) DEFAULT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    estado BOOLEAN DEFAULT TRUE
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabla usuarios creada o ya existente<br>";
} else {
    echo "Error creando la tabla usuarios: " . $conn->error . "<br>";
}

// Crear usuario administrador
$adminEmail = 'obuitragocamelo@yahoo.es';
$adminPassword = 'Obc19447/*';
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol) 
        VALUES ('Administrador', ?, ?, 'admin')
        ON DUPLICATE KEY UPDATE 
        contraseña = VALUES(contraseña),
        rol = VALUES(rol)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $adminEmail, $hashedPassword);

if ($stmt->execute()) {
    echo "Usuario administrador creado o actualizado<br>";
    echo "Email: " . $adminEmail . "<br>";
    echo "Contraseña: " . $adminPassword . "<br>";
} else {
    echo "Error creando usuario administrador: " . $stmt->error . "<br>";
}

$stmt->close();
$conn->close();

echo "<br>Proceso completado. <a href='../frontend/login.php'>Ir al login</a>";
?> 