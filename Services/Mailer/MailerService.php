<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    // Enviar correo genérico (puedes usar HTML en $body si quieres)
    public static function enviarCorreo($to, $subject, $body, $from = 'admin@gmail.com', $fromName = 'Jobyz')
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'localhost';
        $mail->Port = 1025; // MailHog SMTP (desarrollo)
        $mail->SMTPAuth = false;

        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $body;

        // Devuelve true si OK, o el error como string
        return $mail->send() ? true : $mail->ErrorInfo;
    }

    // Ejemplo: Correo de bienvenida
    public static function enviarBienvenida($to, $nombre)
    {
        $subject = "¡Bienvenido a Jobyz!";
        $body = "
            <h2>¡Hola $nombre!</h2>
            <p>Nos alegra que te unas a <b>Jobyz</b>. Ya puedes empezar a navegar por el portal.</p>
        ";
        return self::enviarCorreo($to, $subject, $body);
    }

    // Ejemplo: Recuperar contraseña
    public static function enviarRecuperarPassword($to, $nombre, $token)
    {
        $subject = "Recupera tu contraseña";
        $url = "http://localhost/Jobyz/index.php?page=reset_password&token=$token";
        $body = "
            <h2>Hola $nombre</h2>
            <p>Para recuperar tu contraseña haz clic en este enlace:<br>
            <a href='$url'>$url</a></p>
        ";
        return self::enviarCorreo($to, $subject, $body);
    }

    // Ejemplo: Aviso para administradores
    public static function enviarAvisoAdmin($to, $asunto, $mensaje)
    {
        $subject = "[AVISO Jobyz] $asunto";
        $body = "<p>$mensaje</p>";
        return self::enviarCorreo($to, $subject, $body);
    }
}
