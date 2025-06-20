<?php
require_once 'config.php';

// Verificar si la columna avatar ya existe
$checkColumn = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'avatar'");

if ($checkColumn->num_rows == 0) {
    // Agregar la columna avatar
    $sql = "ALTER TABLE usuarios ADD COLUMN avatar VARCHAR(255) NULL AFTER telefono";
    
    if ($conn->query($sql)) {
        echo "✅ Columna 'avatar' agregada exitosamente a la tabla usuarios.\n";
    } else {
        echo "❌ Error al agregar la columna 'avatar': " . $conn->error . "\n";
    }
} else {
    echo "ℹ️ La columna 'avatar' ya existe en la tabla usuarios.\n";
}

$conn->close();
?> 