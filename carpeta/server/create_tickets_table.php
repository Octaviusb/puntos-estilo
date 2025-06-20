<?php
require_once 'config.php';

echo "<h2>Creando tabla de tickets de canje</h2>";

// Crear tabla de tickets de canje
$sql = "CREATE TABLE IF NOT EXISTS tickets_canje (
    id VARCHAR(50) PRIMARY KEY,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    puntos_usados INT NOT NULL,
    metodo_pago VARCHAR(50),
    puntos_excedentes INT DEFAULT 0,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'confirmado', 'cancelado') DEFAULT 'confirmado',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Tabla tickets_canje creada exitosamente<br>";
} else {
    echo "✗ Error creando tabla tickets_canje: " . $conn->error . "<br>";
}

$conn->close();
echo "<br><strong>¡Proceso completado!</strong><br>";
echo "<a href='../frontend/catalogo.php' style='color: blue; text-decoration: none;'>→ Ir al Catálogo</a>";
?> 