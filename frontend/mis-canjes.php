<?php
session_start();
require_once '../server/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Obtener canjes del usuario
$sql = "SELECT tc.*, p.nombre as producto_nombre, p.imagen as producto_imagen 
        FROM tickets_canje tc 
        JOIN productos p ON tc.producto_id = p.id 
        WHERE tc.usuario_id = ? 
        ORDER BY tc.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user']['id']);
$stmt->execute();
$canjes = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Canjes - Puntos Estilo</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main class="dashboard-container">
        <h2>Mis Canjes</h2>
        
        <div class="exchanges-container">
            <?php if ($canjes->num_rows > 0): ?>
                <div class="exchanges-list">
                    <?php while ($canje = $canjes->fetch_assoc()): ?>
                        <div class="exchange-card">
                            <div class="exchange-header">
                                <div class="exchange-info">
                                    <h3><?php echo htmlspecialchars($canje['producto_nombre']); ?></h3>
                                    <p class="exchange-date"><?php echo date('d/m/Y H:i', strtotime($canje['fecha'])); ?></p>
                                    <p class="exchange-id">Ticket: <?php echo $canje['id']; ?></p>
                                </div>
                                <div class="exchange-status">
                                    <span class="status-badge <?php echo $canje['estado']; ?>">
                                        <?php echo ucfirst($canje['estado']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="exchange-details">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($canje['producto_imagen'] ?: 'img/default-product.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($canje['producto_nombre']); ?>">
                                </div>
                                
                                <div class="exchange-summary">
                                    <div class="summary-item">
                                        <span class="label">Cantidad:</span>
                                        <span class="value"><?php echo $canje['cantidad']; ?></span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="label">Puntos usados:</span>
                                        <span class="value"><?php echo number_format($canje['puntos_usados']); ?></span>
                                    </div>
                                    <?php if ($canje['puntos_excedentes'] > 0): ?>
                                    <div class="summary-item">
                                        <span class="label">Excedente:</span>
                                        <span class="value"><?php echo number_format($canje['puntos_excedentes']); ?> pts</span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="summary-item">
                                        <span class="label">Método de pago:</span>
                                        <span class="value"><?php echo ucfirst($canje['metodo_pago']); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="exchange-actions">
                                <button onclick="printTicket('<?php echo $canje['id']; ?>')" class="btn-small">Imprimir Ticket</button>
                                <button onclick="downloadTicket('<?php echo $canje['id']; ?>')" class="btn-small">Descargar PDF</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-exchanges">
                    <p>Aún no has realizado ningún canje.</p>
                    <a href="catalogo.php" class="btn">Ver Catálogo</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <style>
    .exchanges-container {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .exchanges-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .exchange-card {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .exchange-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .exchange-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .exchange-info h3 {
        margin: 0 0 0.5rem 0;
        color: var(--primary-color);
    }
    
    .exchange-date,
    .exchange-id {
        margin: 0.25rem 0;
        color: #666;
        font-size: 0.9rem;
    }
    
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-badge.confirmado {
        background-color: #27ae60;
        color: white;
    }
    
    .status-badge.pendiente {
        background-color: #f39c12;
        color: white;
    }
    
    .status-badge.cancelado {
        background-color: #e74c3c;
        color: white;
    }
    
    .exchange-details {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .product-image img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .exchange-summary {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.5rem;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem;
        background: var(--light-gray);
        border-radius: 4px;
    }
    
    .summary-item .label {
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .summary-item .value {
        color: var(--secondary-color);
    }
    
    .exchange-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }
    
    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        background-color: var(--secondary-color);
        color: white;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-small:hover {
        opacity: 0.8;
    }
    
    .no-exchanges {
        text-align: center;
        padding: 3rem;
        color: #666;
    }
    
    .no-exchanges .btn {
        margin-top: 1rem;
    }
    </style>

    <script>
    function printTicket(ticketId) {
        // Aquí se implementaría la funcionalidad de impresión
        alert('Funcionalidad de impresión en desarrollo para el ticket: ' + ticketId);
    }
    
    function downloadTicket(ticketId) {
        // Aquí se implementaría la descarga del PDF
        alert('Funcionalidad de descarga PDF en desarrollo para el ticket: ' + ticketId);
    }
    </script>
</body>
</html> 