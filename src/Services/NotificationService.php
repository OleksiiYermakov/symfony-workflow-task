<?php

namespace App\Services;

use App\Entity\Ticket;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private const TEST_EMAIL_SENDER = 'demo@example.com';
    private const TEST_EMAIL_RECEIVER_1 = 'demo1@example.com';
    private const TEST_EMAIL_RECEIVER_2 = 'demo1@example.com';

    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function ticketPaid(Ticket $ticket): void
    {
        $this->sendNotification(
            self::TEST_EMAIL_RECEIVER_1,
            'Ticket was paid',
            'Ticket #' . $ticket->getId() . ' status changed to ' . $ticket->getStatus()
        );
    }

    public function ticketCanceled(Ticket $ticket): void
    {
        $this->sendNotification(
            self::TEST_EMAIL_RECEIVER_2,
            'Ticket was canceled',
            'Ticket #' . $ticket->getId() . ' status changed to ' . $ticket->getStatus()
        );
    }

    private function sendNotification(string $recipient, string $subject, string $content): void
    {
        $email = (new Email())
            ->from(self::TEST_EMAIL_SENDER)
            ->to($recipient)
            ->subject($subject)
            ->text($content);

        $this->mailer->send($email);
    }
}
