<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    public function send(string $to, string $subject, string $textContent, string $htmlContent): void
    {
        $mail = new PHPMailer(true);

        try {
            // Paramètres SMTP Mailtrap
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = '4d7769bfa90123';
            $mail->Password   = 'bf83cddc29850c';
            $mail->Port       = 2525;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Expéditeur et destinataire
            $mail->setFrom('test@evenmonet.tn', 'EvenmoNet');
            $mail->addAddress($to);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlContent;
            $mail->AltBody = $textContent;

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception('Erreur envoi mail : ' . $mail->ErrorInfo);
        }
    }
}
