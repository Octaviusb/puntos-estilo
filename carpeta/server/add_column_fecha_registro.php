<?php
require_once 'config.php';

echo "<h2>Verificando columnas clave en la tabla usuarios</h2>";

// fecha_registro
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'fecha_registro'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE usuarios ADD COLUMN fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'fecha_registro' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna fecha_registro: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'fecha_registro' ya existe<br>";
}

// ultimo_acceso
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'ultimo_acceso'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE usuarios ADD COLUMN ultimo_acceso TIMESTAMP NULL";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'ultimo_acceso' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna ultimo_acceso: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'ultimo_acceso' ya existe<br>";
}

// estado
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'estado'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE usuarios ADD COLUMN estado BOOLEAN DEFAULT TRUE";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'estado' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna estado: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'estado' ya existe<br>";
}

// puntos
$check = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'puntos'");
if ($check->num_rows == 0) {
    $sql = "ALTER TABLE usuarios ADD COLUMN puntos INT DEFAULT 0";
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'puntos' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna puntos: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'puntos' ya existe<br>";
}

$conn->close();
echo "<br><strong>¡Proceso completado!</strong><br>";
echo "<a href='../frontend/gestion-usuarios.php' style='color: blue; text-decoration: none;'>→ Ir a Gestión de Usuarios</a>";
?> 