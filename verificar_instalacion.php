<?php
/**
 * Script de Verificación de Instalación - Puntos Estilo
 * Este script verifica que todos los componentes estén funcionando correctamente
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación de Instalación - Puntos Estilo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #1a237e; margin-bottom: 30px; }
        .check-item { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .summary { margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #3949ab; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔧 Verificación de Instalación</h1>
            <h2>Puntos Estilo</h2>
        </div>";

$checks = [];
$errors = 0;
$warnings = 0;
$success = 0;

// Función para agregar resultados
function addCheck($title, $status, $message, $details = '') {
    global $checks, $errors, $warnings, $success;
    
    $checks[] = [
        'title' => $title,
        'status' => $status,
        'message' => $message,
        'details' => $details
    ];
    
    switch ($status) {
        case 'success': $success++; break;
        case 'error': $errors++; break;
        case 'warning': $warnings++; break;
    }
}

// 1. Verificar versión de PHP
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4.0', '>=')) {
    addCheck('Versión de PHP', 'success', "PHP $phpVersion - Compatible", "Versión mínima requerida: 7.4.0");
} else {
    addCheck('Versión de PHP', 'error', "PHP $phpVersion - Incompatible", "Se requiere PHP 7.4.0 o superior");
}

// 2. Verificar extensiones PHP
$requiredExtensions = ['mysqli', 'session', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        addCheck("Extensión PHP: $ext", 'success', "Instalada", "Extensión $ext disponible");
    } else {
        addCheck("Extensión PHP: $ext", 'error', "No instalada", "Instalar extensión $ext");
    }
}

// 3. Verificar extensiones opcionales
$optionalExtensions = ['gd', 'curl', 'fileinfo'];
foreach ($optionalExtensions as $ext) {
    if (extension_loaded($ext)) {
        addCheck("Extensión PHP: $ext", 'success', "Instalada (opcional)", "Extensión $ext disponible");
    } else {
        addCheck("Extensión PHP: $ext", 'warning', "No instalada (opcional)", "Recomendado instalar $ext para funcionalidad completa");
    }
}

// 4. Verificar archivos críticos
$criticalFiles = [
    'server/config.php' => 'Configuración del sistema',
    'frontend/css/login.css' => 'Estilos del login',
    'frontend/js/login-otp.js' => 'JavaScript del login',
    'frontend/login.php' => 'Página de login',
    'frontend/recuperar-password.php' => 'Recuperación de contraseña',
    'frontend/reset-password.php' => 'Reset de contraseña',
    'includes/create_tables.sql' => 'Script de base de datos'
];

foreach ($criticalFiles as $file => $description) {
    if (file_exists($file)) {
        addCheck("Archivo: $description", 'success', "Existe", "Archivo $file encontrado");
    } else {
        addCheck("Archivo: $description", 'error', "No existe", "Archivo $file no encontrado");
    }
}

// 5. Verificar permisos de directorios
$writableDirs = [
    'frontend/img/' => 'Directorio de imágenes',
    'frontend/uploads/' => 'Directorio de uploads'
];

foreach ($writableDirs as $dir => $description) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            addCheck("Permisos: $description", 'success', "Escritura permitida", "Directorio $dir es escribible");
        } else {
            addCheck("Permisos: $description", 'warning', "Sin permisos de escritura", "Directorio $dir no es escribible");
        }
    } else {
        addCheck("Permisos: $description", 'warning', "Directorio no existe", "Crear directorio $dir");
    }
}

// 6. Verificar conexión a base de datos
try {
    require_once 'server/config.php';
    
    if (isset($conn) && $conn instanceof mysqli) {
        if (!$conn->connect_error) {
            addCheck('Conexión a Base de Datos', 'success', 'Conectado correctamente', "Servidor: " . DB_HOST . ", Base de datos: " . DB_NAME);
            
            // 7. Verificar tablas
            $requiredTables = [
                'usuarios' => 'Tabla de usuarios',
                'password_resets' => 'Tabla de reset de contraseñas',
                'productos' => 'Tabla de productos',
                'transacciones' => 'Tabla de transacciones',
                'canjes' => 'Tabla de canjes',
                'referidos' => 'Tabla de referidos',
                'mensajes' => 'Tabla de mensajes',
                'aliados' => 'Tabla de aliados',
                'bonos' => 'Tabla de bonos',
                'bonos_usuarios' => 'Tabla de bonos de usuarios'
            ];
            
            foreach ($requiredTables as $table => $description) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    addCheck("Tabla: $description", 'success', "Existe", "Tabla $table encontrada");
                } else {
                    addCheck("Tabla: $description", 'error', "No existe", "Tabla $table no encontrada");
                }
            }
            
            // 8. Verificar usuario administrador
            $result = $conn->query("SELECT * FROM usuarios WHERE correo = 'admin@puntosestilo.com' AND rol = 'admin'");
            if ($result && $result->num_rows > 0) {
                addCheck('Usuario Administrador', 'success', 'Creado correctamente', 'admin@puntosestilo.com / password');
            } else {
                addCheck('Usuario Administrador', 'error', 'No encontrado', 'Ejecutar script create_tables.sql');
            }
            
        } else {
            addCheck('Conexión a Base de Datos', 'error', 'Error de conexión', $conn->connect_error);
        }
    } else {
        addCheck('Conexión a Base de Datos', 'error', 'Configuración incorrecta', 'Verificar server/config.php');
    }
} catch (Exception $e) {
    addCheck('Conexión a Base de Datos', 'error', 'Excepción', $e->getMessage());
}

// 9. Verificar configuración de sesiones
if (function_exists('session_start')) {
    addCheck('Sesiones PHP', 'success', 'Disponible', 'Sistema de sesiones funcional');
} else {
    addCheck('Sesiones PHP', 'error', 'No disponible', 'Verificar configuración de PHP');
}

// 10. Verificar zona horaria
$timezone = date_default_timezone_get();
if ($timezone) {
    addCheck('Zona Horaria', 'success', "Configurada: $timezone", 'Zona horaria establecida');
} else {
    addCheck('Zona Horaria', 'warning', 'No configurada', 'Configurar zona horaria en php.ini');
}

// Mostrar resultados
foreach ($checks as $check) {
    $statusClass = $check['status'];
    echo "<div class='check-item $statusClass'>
        <strong>{$check['title']}</strong><br>
        <span>{$check['message']}</span>";
    if ($check['details']) {
        echo "<br><small>{$check['details']}</small>";
    }
    echo "</div>";
}

// Resumen
echo "<div class='summary'>
    <h3>📊 Resumen de Verificación</h3>
    <p><strong>✅ Exitosos:</strong> $success</p>
    <p><strong>⚠️ Advertencias:</strong> $warnings</p>
    <p><strong>❌ Errores:</strong> $errors</p>";

if ($errors == 0 && $warnings == 0) {
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡Instalación completada exitosamente!</p>";
    echo "<a href='frontend/' class='btn'>Ir al Sistema</a>";
} elseif ($errors == 0) {
    echo "<p style='color: #ffc107; font-weight: bold;'>⚠️ Instalación funcional con advertencias menores</p>";
    echo "<a href='frontend/' class='btn'>Ir al Sistema</a>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Hay errores que deben corregirse antes de usar el sistema</p>";
    echo "<a href='INSTALACION.md' class='btn'>Ver Guía de Instalación</a>";
    echo "<a href='ejecutar_sql.php' class='btn' style='background: #28a745;'>🔧 Ejecutar Script SQL</a>";
}

echo "</div>";

// Información adicional
echo "<div class='info' style='margin-top: 20px; padding: 15px;'>
    <h4>📝 Información Adicional</h4>
    <p><strong>Credenciales por defecto:</strong></p>
    <ul>
        <li><strong>Email:</strong> admin@puntosestilo.com</li>
        <li><strong>Contraseña:</strong> password</li>
        <li><strong>Rol:</strong> Administrador</li>
    </ul>
    <p><strong>Nota:</strong> Cambia la contraseña del administrador después del primer login.</p>
</div>";

echo "</div></body></html>";
?> 