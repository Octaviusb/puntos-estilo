<?php
class EmailService {
    private $smtp_host = 'localhost'; // Cambiar por tu servidor SMTP
    private $smtp_port = 587;
    private $smtp_username = 'noreply@puntosestilo.com';
    private $smtp_password = 'tu_password';
    private $from_email = 'noreply@puntosestilo.com';
    private $from_name = 'Puntos Estilo';
    
    public function __construct() {
        // Configuración básica para usar mail() de PHP
        // Para un entorno de producción, usar PHPMailer o similar
    }
    
    /**
     * Enviar notificación de canje exitoso
     */
    public function enviarNotificacionCanje($usuario, $producto, $cantidad, $puntosUsados, $ticketId) {
        $subject = "Canje Exitoso - Puntos Estilo";
        
        $message = "
        <html>
        <head>
            <title>Canje Exitoso</title>
        </head>
        <body>
            <h2>¡Canje Exitoso!</h2>
            <p>Hola {$usuario['nombre']},</p>
            <p>Tu canje ha sido procesado exitosamente.</p>
            
            <h3>Detalles del Canje:</h3>
            <ul>
                <li><strong>Producto:</strong> {$producto['nombre']}</li>
                <li><strong>Cantidad:</strong> {$cantidad}</li>
                <li><strong>Puntos utilizados:</strong> " . number_format($puntosUsados) . "</li>
                <li><strong>Ticket ID:</strong> {$ticketId}</li>
                <li><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</li>
            </ul>
            
            <p><strong>Saldo actual:</strong> " . number_format($usuario['puntos']) . " puntos</p>
            
            <p>Gracias por usar Puntos Estilo.</p>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($usuario['email'], $subject, $message);
    }
    
    /**
     * Enviar notificación de carga de puntos
     */
    public function enviarNotificacionCargaPuntos($usuario, $puntosCargados, $motivo = '') {
        $subject = "Puntos Cargados - Puntos Estilo";
        
        $message = "
        <html>
        <head>
            <title>Puntos Cargados</title>
        </head>
        <body>
            <h2>Puntos Cargados</h2>
            <p>Hola {$usuario['nombre']},</p>
            <p>Se han cargado puntos a tu cuenta.</p>
            
            <h3>Detalles:</h3>
            <ul>
                <li><strong>Puntos cargados:</strong> " . number_format($puntosCargados) . "</li>
                <li><strong>Saldo actual:</strong> " . number_format($usuario['puntos']) . "</li>
                <li><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</li>
            </ul>
            
            " . ($motivo ? "<p><strong>Motivo:</strong> {$motivo}</p>" : "") . "
            
            <p>Gracias por usar Puntos Estilo.</p>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($usuario['email'], $subject, $message);
    }
    
    /**
     * Enviar notificación de descuento de puntos
     */
    public function enviarNotificacionDescuentoPuntos($usuario, $puntosDescontados, $motivo = '') {
        $subject = "Puntos Descontados - Puntos Estilo";
        
        $message = "
        <html>
        <head>
            <title>Puntos Descontados</title>
        </head>
        <body>
            <h2>Puntos Descontados</h2>
            <p>Hola {$usuario['nombre']},</p>
            <p>Se han descontado puntos de tu cuenta.</p>
            
            <h3>Detalles:</h3>
            <ul>
                <li><strong>Puntos descontados:</strong> " . number_format($puntosDescontados) . "</li>
                <li><strong>Saldo actual:</strong> " . number_format($usuario['puntos']) . "</li>
                <li><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</li>
            </ul>
            
            " . ($motivo ? "<p><strong>Motivo:</strong> {$motivo}</p>" : "") . "
            
            <p>Si tienes alguna pregunta, contacta con soporte.</p>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($usuario['email'], $subject, $message);
    }
    
    /**
     * Enviar notificación de vencimiento de puntos
     */
    public function enviarNotificacionVencimientoPuntos($usuario, $puntosVencidos) {
        $subject = "Puntos por Vencer - Puntos Estilo";
        
        $message = "
        <html>
        <head>
            <title>Puntos por Vencer</title>
        </head>
        <body>
            <h2>Puntos por Vencer</h2>
            <p>Hola {$usuario['nombre']},</p>
            <p>Tienes puntos que vencerán próximamente.</p>
            
            <h3>Detalles:</h3>
            <ul>
                <li><strong>Puntos por vencer:</strong> " . number_format($puntosVencidos) . "</li>
                <li><strong>Fecha de vencimiento:</strong> " . date('d/m/Y', strtotime('+30 days')) . "</li>
            </ul>
            
            <p>¡No olvides usar tus puntos antes de que venzan!</p>
            <p>Visita nuestro catálogo para ver las opciones disponibles.</p>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($usuario['email'], $subject, $message);
    }
    
    /**
     * Enviar email usando la función mail() de PHP
     */
    private function enviarEmail($to, $subject, $message) {
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'X-Mailer: PHP/' . phpversion()
        );
        
        // En un entorno de desarrollo, solo loguear el email
        if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
            error_log("EMAIL SIMULADO - Para: {$to}, Asunto: {$subject}");
            return true;
        }
        
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Enviar notificación de bienvenida
     */
    public function enviarBienvenida($usuario) {
        $subject = "¡Bienvenido a Puntos Estilo!";
        
        $message = "
        <html>
        <head>
            <title>Bienvenido</title>
        </head>
        <body>
            <h2>¡Bienvenido a Puntos Estilo!</h2>
            <p>Hola {$usuario['nombre']},</p>
            <p>¡Gracias por registrarte en nuestro programa de puntos!</p>
            
            <p>Con tu cuenta podrás:</p>
            <ul>
                <li>Acumular puntos en tus compras</li>
                <li>Canjear productos del catálogo</li>
                <li>Recibir ofertas especiales</li>
                <li>Acceder a beneficios exclusivos</li>
            </ul>
            
            <p>¡Comienza a disfrutar de todos los beneficios!</p>
        </body>
        </html>
        ";
        
        return $this->enviarEmail($usuario['email'], $subject, $message);
    }
}
?> 