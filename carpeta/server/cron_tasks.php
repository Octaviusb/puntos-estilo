<?php
require_once 'config.php';
require_once 'email_service.php';

echo "<h2>Ejecutando Tareas Programadas</h2>";

$emailService = new EmailService();
$tasksExecuted = 0;

// 1. Notificar puntos por vencer (30 días antes)
echo "<h3>1. Verificando puntos por vencer...</h3>";

// Nota: Esta es una implementación básica. En un sistema real, necesitarías
// una tabla de puntos con fechas de vencimiento específicas
$sql = "SELECT u.*, 
        (SELECT SUM(puntos) FROM transacciones 
         WHERE usuario_id = u.id AND tipo = 'carga' 
         AND fecha >= DATE_SUB(NOW(), INTERVAL 11 MONTH)) as puntos_por_vencer
        FROM usuarios u 
        WHERE u.rol = 'usuario' AND u.puntos > 0";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($usuario = $result->fetch_assoc()) {
        if ($usuario['puntos_por_vencer'] > 0) {
            // Verificar si ya se envió notificación recientemente
            $sqlCheck = "SELECT COUNT(*) as count FROM notificaciones 
                        WHERE usuario_id = ? AND tipo = 'vencimiento' 
                        AND fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $stmt = $conn->prepare($sqlCheck);
            $stmt->bind_param("i", $usuario['id']);
            $stmt->execute();
            $yaNotificado = $stmt->get_result()->fetch_assoc()['count'] > 0;
            
            if (!$yaNotificado) {
                $emailService->enviarNotificacionVencimientoPuntos($usuario, $usuario['puntos_por_vencer']);
                
                // Registrar notificación
                $sqlNotif = "INSERT INTO notificaciones (usuario_id, tipo, descripcion, fecha) 
                             VALUES (?, 'vencimiento', ?, NOW())";
                $stmt = $conn->prepare($sqlNotif);
                $descripcion = "Notificación de vencimiento: " . number_format($usuario['puntos_por_vencer']) . " puntos";
                $stmt->bind_param("is", $usuario['id'], $descripcion);
                $stmt->execute();
                
                echo "✓ Notificación de vencimiento enviada a: {$usuario['email']}<br>";
                $tasksExecuted++;
            }
        }
    }
} else {
    echo "No hay usuarios con puntos por vencer.<br>";
}

// 2. Limpiar notificaciones antiguas (más de 6 meses)
echo "<h3>2. Limpiando notificaciones antiguas...</h3>";

$sql = "DELETE FROM notificaciones WHERE fecha < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
if ($conn->query($sql)) {
    $affected = $conn->affected_rows;
    echo "✓ Se eliminaron {$affected} notificaciones antiguas.<br>";
    $tasksExecuted++;
} else {
    echo "✗ Error limpiando notificaciones: " . $conn->error . "<br>";
}

// 3. Generar reporte diario para administradores
echo "<h3>3. Generando reporte diario...</h3>";

$sql = "SELECT 
        COUNT(DISTINCT tc.usuario_id) as usuarios_activos,
        COUNT(tc.id) as total_canjes,
        SUM(tc.puntos_usados) as puntos_canjeados,
        COUNT(DISTINCT t.usuario_id) as usuarios_transacciones,
        SUM(CASE WHEN t.tipo = 'carga' THEN t.puntos ELSE 0 END) as puntos_cargados,
        SUM(CASE WHEN t.tipo = 'redencion' THEN t.puntos ELSE 0 END) as puntos_redimidos
        FROM tickets_canje tc
        LEFT JOIN transacciones t ON DATE(tc.fecha) = DATE(t.fecha)
        WHERE DATE(tc.fecha) = CURDATE()";

$result = $conn->query($sql);
$reporte = $result->fetch_assoc();

if ($reporte) {
    $reporteHtml = "
    <h2>Reporte Diario - " . date('d/m/Y') . "</h2>
    <ul>
        <li>Usuarios activos: {$reporte['usuarios_activos']}</li>
        <li>Total canjes: {$reporte['total_canjes']}</li>
        <li>Puntos canjeados: " . number_format($reporte['puntos_canjeados']) . "</li>
        <li>Puntos cargados: " . number_format($reporte['puntos_cargados']) . "</li>
        <li>Puntos redimidos: " . number_format($reporte['puntos_redimidos']) . "</li>
    </ul>
    ";
    
    // Guardar reporte en base de datos
    $sqlReporte = "INSERT INTO reportes_diarios (fecha, contenido, tipo) VALUES (CURDATE(), ?, 'diario')";
    $stmt = $conn->prepare($sqlReporte);
    $stmt->bind_param("s", $reporteHtml);
    $stmt->execute();
    
    echo "✓ Reporte diario generado y guardado.<br>";
    $tasksExecuted++;
}

// 4. Verificar productos con stock bajo
echo "<h3>4. Verificando productos con stock bajo...</h3>";

$sql = "SELECT * FROM productos WHERE stock <= 5 AND estado = TRUE";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Productos con stock bajo:<br>";
    while ($producto = $result->fetch_assoc()) {
        echo "- {$producto['nombre']}: {$producto['stock']} unidades<br>";
    }
    
    // Aquí podrías enviar notificación a administradores
    echo "✓ Alerta de stock bajo generada.<br>";
    $tasksExecuted++;
} else {
    echo "No hay productos con stock bajo.<br>";
}

$conn->close();

echo "<br><strong>¡Tareas completadas!</strong> Se ejecutaron {$tasksExecuted} tareas.<br>";
echo "<a href='../frontend/administracion.php' style='color: blue; text-decoration: none;'>→ Volver al Panel de Administración</a>";
?> 