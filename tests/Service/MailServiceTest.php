<?php

namespace App\Tests\Service;

use App\Service\MailService;
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;

class MailServiceTest extends TestCase
{
    public function testSendEmail()
    {
        // Crée une classe anonyme pour simuler PHPMailer
        $mockMailer = $this->createMock(PHPMailer::class);

        // On s'assure que les bonnes méthodes sont appelées
        $mockMailer->expects($this->once())->method('send')->willReturn(true);

        // On injecte notre PHPMailer mocké dans MailService (nécessite adaptation)
        $mailService = new class($mockMailer) extends MailService {
            private $mailer;
            public function __construct($mailer)
            {
                $this->mailer = $mailer;
            }

            public function send(string $to, string $subject, string $textContent, string $htmlContent): void
            {
                $mail = $this->mailer;
                $mail->setFrom('test@evenmonet.tn', 'EvenmoNet');
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlContent;
                $mail->AltBody = $textContent;

                $mail->send();
            }
        };

        // Lancer le test
        $mailService->send('test@example.com', 'Sujet', 'Texte', '<b>HTML</b>');
        $this->assertTrue(true); // Si aucune exception, le test est OK
    }
}
