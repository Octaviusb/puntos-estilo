<?php
/**
 * Script para ejecutar automáticamente el SQL de creación de tablas
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Ejecutando SQL - Puntos Estilo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #1a237e; margin-bottom: 30px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .btn { display: inline-block; padding: 10px 20px; background: #1a237e; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }
        .btn:hover { background: #3949ab; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔧 Ejecutando Script SQL</h1>
            <h2>Puntos Estilo</h2>
        </div>";

try {
    // Incluir configuración de base de datos
    require_once 'server/config.php';
    
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
    
    // Leer el archivo SQL
    $sqlFile = 'includes/create_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo SQL no encontrado: $sqlFile");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    if (!$sqlContent) {
        throw new Exception("No se pudo leer el archivo SQL");
    }
    
    // Dividir el SQL en comandos individuales
    $commands = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    $successCount = 0;
    $errorCount = 0;
    $errors = [];
    
    echo "<h3>📋 Ejecutando comandos SQL...</h3>";
    
    foreach ($commands as $command) {
        if (empty($command) || strpos($command, '--') === 0) {
            continue; // Saltar comentarios y líneas vacías
        }
        
        try {
            if ($conn->query($command)) {
                $successCount++;
                echo "<div class='success'>✅ Comando ejecutado correctamente</div>";
            } else {
                $errorCount++;
                $errors[] = $conn->error;
                echo "<div class='error'>❌ Error: " . htmlspecialchars($conn->error) . "</div>";
            }
        } catch (Exception $e) {
            $errorCount++;
            $errors[] = $e->getMessage();
            echo "<div class='error'>❌ Excepción: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<h3>📊 Resumen de Ejecución</h3>";
    echo "<p><strong>✅ Comandos exitosos:</strong> $successCount</p>";
    echo "<p><strong>❌ Errores:</strong> $errorCount</p>";
    
    if ($errorCount == 0) {
        echo "<div class='success'>
            <h4>🎉 ¡Script ejecutado exitosamente!</h4>
            <p>Todas las tablas han sido creadas correctamente.</p>
        </div>";
    } else {
        echo "<div class='error'>
            <h4>⚠️ Se encontraron algunos errores</h4>
            <p>Algunos comandos no se ejecutaron correctamente.</p>
        </div>";
    }
    
    // Verificar si las tablas se crearon
    echo "<h3>🔍 Verificando Tablas Creadas</h3>";
    
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