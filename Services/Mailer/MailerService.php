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
        $subject = "Bienvenido a Jobyz";
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

    public static function enviarAceptacionSolicitud($to, $nombre, $nombreOferta, $empresaCorreo, $empresaNombre)
    {
        $subject = "Tu solicitud en Jobyz ha sido ACEPTADA";
        $body = "
            <h2>¡Hola $nombre!</h2>
            <p>Nos complace informarte que tu solicitud para la oferta <b>$nombreOferta</b> ha sido <b>aceptada</b>.</p>
            <p>En breve se pondrán en contacto contigo desde la empresa <b>$empresaNombre</b> o puedes consultar tu panel de usuario para más información.</p>
            <br>
            <p>Equipo Jobyz</p>
        ";
        return self::enviarCorreo($to, $subject, $body, $empresaCorreo, $empresaNombre);
    }

    // Correo al rechazar solicitud (desde empresa)
    public static function enviarRechazoSolicitud($to, $nombre, $nombreOferta, $empresaCorreo, $empresaNombre)
    {
        $subject = "Tu solicitud en Jobyz ha sido RECHAZADA";
        $body = "
            <h2>Hola $nombre,</h2>
            <p>Lamentamos informarte que tu solicitud para la oferta <b>$nombreOferta</b> ha sido <b>rechazada</b> por la empresa <b>$empresaNombre</b>.</p>
            <p>Puedes seguir viendo otras ofertas y oportunidades en Jobyz.</p>
            <br>
            <p>¡Ánimo y mucha suerte en futuras candidaturas!<br>
            Equipo Jobyz</p>
        ";
        return self::enviarCorreo($to, $subject, $body, $empresaCorreo, $empresaNombre);
    }

    public static function enviarAvisoNoValidado($to, $nombre)
    {
        $subject = "Completa el registro de tu cuenta Jobyz";
        $body = "
            <h2>Hola $nombre,</h2>
            <p>Hemos creado tu cuenta en <b>Jobyz</b>, pero aún no has terminado de registrar tus datos ni has iniciado sesión en la plataforma.</p>
            <p>Por favor, accede al portal de alumnos para completar tu información personal y activar correctamente tu cuenta. Solo así podrás acceder a todas las oportunidades y gestionar tus solicitudes.</p>
            <p>Si tienes dudas, contacta con el centro o con el soporte de Jobyz.</p>
            <br>
            <p>Un saludo,<br>El equipo Jobyz</p>
        ";
        return self::enviarCorreo($to, $subject, $body);
    }

    public static function enviarBienvenidaMasiva($to, $nombre, $email, $passTemporal = 'Temporal1234')
    {
        $subject = "Tu cuenta Jobyz ha sido creada";
        $body = "
            <h2>¡Hola $nombre!</h2>
            <p>Te informamos que se ha creado tu cuenta en <b>Jobyz</b>.</p>
            <p>Estos son tus datos de acceso inicial:</p>
            <ul>
                <li><b>Usuario (email):</b> $email</li>
                <li><b>Contraseña temporal:</b> $passTemporal</li>
            </ul>
            <p>Por tu seguridad, cambia tu contraseña y completa tu información personal en cuanto entres.</p>
            <br>
            <p>¡Bienvenido!<br>Equipo Jobyz</p>
        ";
        return self::enviarCorreo($to, $subject, $body);
    }
}
