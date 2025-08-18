<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(EntityManagerInterface $em, TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();

        // Événements organisés
        $userEvents = $em->getRepository(Annonce::class)->findBy(['user' => $user]);

        // Tickets achetés par l'utilisateur
        $userTickets = $ticketRepository->findBy(['user' => $user], ['purchasedAt' => 'DESC']);

        return $this->render('home/profile.html.twig', [
            'userEvents' => $userEvents,
            'userTickets' => $userTickets,
        ]);
    }
}
