<?php
require_once 'config.php';

echo "<h2>Agregando columna 'puntos' a la tabla usuarios</h2>";

// Verificar si la columna 'puntos' existe
$checkColumn = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'puntos'");

if ($checkColumn->num_rows == 0) {
    // Agregar la columna 'puntos'
    $sql = "ALTER TABLE usuarios ADD COLUMN puntos INT DEFAULT 0";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'puntos' agregada exitosamente<br>";
        
        // Actualizar el usuario administrador con algunos puntos de ejemplo
        $sql = "UPDATE usuarios SET puntos = 1000 WHERE correo = 'obuitragocamelo@yahoo.es'";
        if ($conn->query($sql) === TRUE) {
            echo "✓ Usuario administrador actualizado con 1000 puntos<br>";
        }
    } else {
        echo "✗ Error agregando columna: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'puntos' ya existe<br>";
}

// Verificar si la columna 'estado' existe
$checkEstado = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'estado'");

if ($checkEstado->num_rows == 0) {
    // Agregar la columna 'estado'
    $sql = "ALTER TABLE usuarios ADD COLUMN estado BOOLEAN DEFAULT TRUE";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'estado' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna estado: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'estado' ya existe<br>";
}

// Verificar si la columna 'ultimo_acceso' existe
$checkUltimoAcceso = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'ultimo_acceso'");

if ($checkUltimoAcceso->num_rows == 0) {
    // Agregar la columna 'ultimo_acceso'
    $sql = "ALTER TABLE usuarios ADD COLUMN ultimo_acceso TIMESTAMP NULL";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Columna 'ultimo_acceso' agregada exitosamente<br>";
    } else {
        echo "✗ Error agregando columna ultimo_acceso: " . $conn->error . "<br>";
    }
} else {
    echo "✓ La columna 'ultimo_acceso' ya existe<br>";
}

$conn->close();

echo "<br><strong>¡Proceso completado!</strong><br>";
echo "<a href='../frontend/administracion.php' style='color: blue; text-decoration: none;'>→ Ir a Administración</a>";
?> 