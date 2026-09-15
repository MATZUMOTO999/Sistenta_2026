<?php

// app/utils/Mailer.php
namespace App\Utils;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Sigi\DatosSistema;

class Mailer
{
    public static function send(string $toEmail, string $toName, string $subject, string $html, string $alt = ''): array
    {
        $cfg = (new DatosSistema())->buscar();
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $cfg['host_mail'];             // Debe ser HOST SMTP (no un correo)
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['email_email'];
            $mail->Password   = $cfg['password_email'];
            $port = (int)($cfg['puerto_email'] ?: 587);
            $mail->Port       = $port;
            $mail->SMTPSecure = ($port === 587)
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ]
            ];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($cfg['email_email'], $cfg['nombre_corto'] ?: 'SIGI');
            $mail->Sender   = $cfg['email_email']; // Return-Path
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body    = $html;
            $mail->AltBody = $alt ?: strip_tags($html);

            $ok = $mail->send();
            $mid = $mail->getLastMessageID(); // puede ser null si falla antes
            return ['ok' => $ok, 'error' => $ok ? null : $mail->ErrorInfo, 'mid' => $mid];
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        }
    }
}
