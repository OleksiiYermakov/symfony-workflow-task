<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Ticket;
use App\Services\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class TicketWorkflowEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationService $notificationService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.ticket_workflow.transition.paid' => 'paid',
            'workflow.ticket_workflow.transition.canceled' => 'canceled',
        ];
    }

    public function paid(Event $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->getSubject();
        $this->notificationService->ticketPaid($ticket);
    }

    public function canceled(Event $event): void
    {
        /** @var Ticket $ticket */
        $ticket = $event->getSubject();
        $this->notificationService->ticketCanceled($ticket);
    }
}
