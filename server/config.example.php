<?php
// Configuración de ejemplo - NO usar en producción
$db_host = 'localhost';
$db_user = 'your_username';
$db_pass = 'your_password';
$db_name = 'your_database';

// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8");

// Configuración de la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores (deshabilitar en producción)
error_reporting(0);
ini_set('display_errors', 0);

// Función para sanitizar datos
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

// Función para verificar si el usuario es administrador
function isAdmin() {
    return isset($_SESSION['user']['rol']) && $_SESSION['user']['rol'] === 'admin';
}

// Función para redirigir
function redirect($url) {
    header("Location: $url");
    exit();
}
?> 