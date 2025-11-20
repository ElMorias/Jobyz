<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * MailerService
 *
 * Servicio estático para el envío de correos electrónicos (notificaciones y avisos)
 * usando PHPMailer y el SMTP configurado (por defecto MailHog para desarrollo local).
 */
class MailerService
{
    /**
     * Envía un correo genérico usando PHPMailer.
     *
     * @param string $to       Correo destinatario
     * @param string $subject  Asunto del mensaje
     * @param string $body     Cuerpo HTML del mensaje
     * @param string $from     Correo remitente (por defecto: admin@gmail.com)
     * @param string $fromName Nombre remitente (por defecto: Jobyz)
     * @return bool|string     True en caso de éxito, o mensaje de error
     */
    public static function enviarCorreo($to, $subject, $body, $from = 'admin@gmail.com', $fromName = 'Jobyz')
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'mailhog';
        $mail->Port = 1025; // MailHog SMTP (desarrollo)
        $mail->SMTPAuth = false;

        $mail->setFrom($from, $fromName);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $body;

        // Devuelve true si OK, o el error como string
        return $mail->send() ? true : $mail->ErrorInfo;
    }

    /**
     * Envía correo de bienvenida tras el registro de usuario.
     * @param string $to     Email receptor
     * @param string $nombre Nombre del nuevo usuario
     * @return bool|string
     */
    public static function enviarBienvenida($to, $nombre)
    {
        $subject = "Bienvenido a Jobyz";
        $body = "
            <h2>¡Hola $nombre!</h2>
            <p>Nos alegra que te unas a <b>Jobyz</b>. Ya puedes empezar a navegar por el portal.</p>
        ";
        return self::enviarCorreo($to, $subject, $body);
    }

    /**
     * Envía instrucciones de recuperación de contraseña con enlace único.
     * @param string $to     Email receptor
     * @param string $nombre Nombre usuario
     * @param string $token  Token seguro del enlace
     * @return bool|string
     */
    public static function enviarRecuperarPassword($to, $nombre, $token)
    {
    }

    /**
     * Notifica al alumno que su solicitud fue aceptada.
     * @param string $to             Email alumno
     * @param string $nombre         Nombre alumno
     * @param string $nombreOferta   Título de la oferta
     * @param string $empresaCorreo  Email de la empresa
     * @param string $empresaNombre  Nombre empresa
     * @return bool|string
     */
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

    /**
     * Notifica al alumno que su solicitud fue rechazada.
     * @param string $to             Email alumno
     * @param string $nombre         Nombre alumno
     * @param string $nombreOferta   Título de la oferta
     * @param string $empresaCorreo  Email de la empresa
     * @param string $empresaNombre  Nombre empresa
     * @return bool|string
     */
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

    /**
     * Envía email recordatorio para cuentas de alumno creadas pero no completadas.
     * @param string $to     Email alumno
     * @param string $nombre Nombre alumno
     * @return bool|string
     */
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

    /**
     * Envío masivo utilizado en carga múltiple de usuarios/alumnos.
     * @param string $to           Email alumno
     * @param string $nombre       Nombre alumno
     * @param string $email        Email alumno (repetido por compatibilidad)
     * @param string $passTemporal Contraseña temporal (por defecto Temporal1234)
     * @return bool|string
     */
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
