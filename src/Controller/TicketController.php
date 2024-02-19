<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\Registry;

#[Route('/')]
class TicketController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TicketRepository $ticketRepository,
        private Registry $workflow,
    ) {
    }

    #[Route('/', name: 'ticket_index', methods: ['GET'])]
    public function index(): Response
    {
        $tickets = $this->ticketRepository->findAll();

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets
        ]);
    }

    #[Route('/create', name: 'create_ticket', methods: ['POST'])]
    public function create(): RedirectResponse
    {
        $ticket = new Ticket();
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return $this->redirect($this->generateUrl('ticket_view', [
            'id' => $ticket->getId(),
        ]));
    }

    #[Route('/view/{id<\d+>}', name: 'ticket_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $ticket = $this->findTicket($id);

        return $this->render('ticket/view.html.twig', [
            'ticket' => $ticket,
            'workflowName' => 'ticket_workflow',
        ]);
    }

    #[Route('/change-status/{id<\d+>}/{transition}', name: 'ticket_change_status', methods: ['GET'])]
    public function changeStatus(int $id, string $transition): Response
    {
        $ticket = $this->findTicket($id);
        $workflow = $this->workflow->get($ticket);

        if (!$workflow->can($ticket, $transition)) {
            throw $this->createNotFoundException('You can\'t change ticket status to ' . $transition);
        }

        $workflow->apply($ticket, $transition);
        $this->entityManager->flush();

        return $this->redirect($this->generateUrl('ticket_view', [
            'id' => $ticket->getId(),
        ]));
    }

    private function findTicket(int $id): Ticket
    {
        $ticket = $this->ticketRepository->find($id);

        if (!$ticket) {
            throw $this->createNotFoundException('No ticket found for id ' . $id);
        }

        return $ticket;
    }
}
