<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Si no usas Composer, incluye los archivos manualmente:
require './PHPMailer/PHPMailer.php';
require './PHPMailer/Exception.php';
require './PHPMailer/SMTP.php';

// Crear instancia
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';      // Servidor SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'wiliamsyos@gmail.com.gt'; // Tu correo
    $mail->Password   = '123'; // Contraseña o "App Password"
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('wiliamsyos@gmail.com.gt', 'Tienda tortuga');
    //$mail->addAddress('wiliamsyos@gmail.com', 'Tienda Ejemplo');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Confirmación de Pedido';
    $mail->Body    = '
        <h2>Gracias por tu compra</h2>
        <p>Tu pedido ha sido procesado correctamente.</p>
        <p><strong>Fecha:</strong> ' . date('d/m/Y H:i') . '</p>
    ';
    $mail->AltBody = 'Tu pedido ha sido procesado correctamente.';

    // Enviar correo
    $mail->send();
    echo '✅ Correo enviado correctamente.';
} catch (Exception $e) {
    echo "❌ Error al enviar el correo: {$mail->ErrorInfo}";
}
