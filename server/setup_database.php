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

echo "<h2>Configurando Base de Datos Puntos Estilo</h2>";

// Crear la base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS mi_proyecto";
if ($conn->query($sql) === TRUE) {
    echo "✓ Base de datos creada o ya existente<br>";
} else {
    echo "✗ Error creando la base de datos: " . $conn->error . "<br>";
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
    echo "✓ Tabla usuarios creada o ya existente<br>";
} else {
    echo "✗ Error creando la tabla usuarios: " . $conn->error . "<br>";
}

// Crear tabla de transacciones
$sql = "CREATE TABLE IF NOT EXISTS transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('acumulacion', 'redencion') NOT NULL,
    puntos INT NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla transacciones creada o ya existente<br>";
} else {
    echo "✗ Error creando la tabla transacciones: " . $conn->error . "<br>";
}

// Crear tabla de productos
$sql = "CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    puntos INT NOT NULL,
    destacado BOOLEAN DEFAULT FALSE,
    stock INT DEFAULT 0,
    estado BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla productos creada o ya existente<br>";
} else {
    echo "✗ Error creando la tabla productos: " . $conn->error . "<br>";
}

// Crear tabla de mensajes de contacto
$sql = "CREATE TABLE IF NOT EXISTS mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    celular VARCHAR(20) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('nuevo', 'leido', 'respondido') DEFAULT 'nuevo'
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla mensajes_contacto creada o ya existente<br>";
} else {
    echo "✗ Error creando la tabla mensajes_contacto: " . $conn->error . "<br>";
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
    echo "✓ Usuario administrador creado o actualizado<br>";
    echo "&nbsp;&nbsp;&nbsp;Email: " . $adminEmail . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Contraseña: " . $adminPassword . "<br>";
} else {
    echo "✗ Error creando usuario administrador: " . $stmt->error . "<br>";
}

// Insertar algunos productos de ejemplo
$productos = [
    ['Perro Altoque', 'Delicioso perro caliente con todos los ingredientes', 'img/perro.jpg', 1450, TRUE, 50],
    ['Bono Combustible $5.000', 'Bono de combustible por valor de $5.000', 'img/bono-5000.jpg', 1500, TRUE, 100],
    ['Bono Combustible $1.000', 'Bono de combustible por valor de $1.000', 'img/bono-1000.jpg', 300, TRUE, 200],
    ['Bono Combustible $10.000', 'Bono de combustible por valor de $10.000', 'img/bono-10000.jpg', 3000, TRUE, 50]
];

$sql = "INSERT INTO productos (nombre, descripcion, imagen, puntos, destacado, stock) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

foreach ($productos as $producto) {
    $stmt->bind_param("sssiii", $producto[0], $producto[1], $producto[2], $producto[3], $producto[4], $producto[5]);
    if ($stmt->execute()) {
        echo "✓ Producto agregado: " . $producto[0] . "<br>";
    } else {
        echo "✗ Error agregando producto: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();

echo "<br><strong>¡Configuración completada!</strong><br>";
echo "<a href='../frontend/login.php' style='color: blue; text-decoration: none;'>→ Ir al Login</a>";
?> 