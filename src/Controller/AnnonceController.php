<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Ticket;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\TicketRepository;
use App\Service\MailService;




#[Route('/annonce')]
final class AnnonceController extends AbstractController
{
    #[Route(name: 'app_annonce_index', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/organiser', name: 'app_annonce_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $annonce = new Annonce();
        $annonce->setUser($this->getUser());
        $annonce->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', 'Votre demande a été soumise avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenements', name: 'app_evenements', methods: ['GET'])]
    public function evenements(
        AnnonceRepository $annonceRepository,
        TicketRepository $ticketRepository,
        UserInterface $user = null
    ): Response {
        $annonces = $annonceRepository->findBy(['isApproved' => true]);

        // Récupérer les IDs des annonces dont l'utilisateur a acheté le ticket
        $ticketsAchetes = [];
        if ($user) {
            foreach ($annonces as $annonce) {
                if ($ticketRepository->hasUserBoughtTicket($user, $annonce)) {
                    $ticketsAchetes[] = $annonce->getId();
                }
            }
        }

        return $this->render('event/show.html.twig', [
            'annonces' => $annonces,
            'ticketsAchetes' => $ticketsAchetes,
        ]);
    }


    #[Route('/acheter-ticket/{id}', name: 'acheter_ticket')]
    public function acheterTicket(Annonce $annonce, MailService $mailService, UserInterface $user, EntityManagerInterface $em): Response
    {
        if (!$user instanceof \App\Entity\User) {
            throw new \LogicException('Utilisateur non valide');
        }

        // Créer un ticket
        $ticket = new Ticket();
        $ticket->setUser($user);
        $ticket->setAnnonce($annonce);
        $ticket->setPurchasedAt(new \DateTimeImmutable());

        $em->persist($ticket);
        $em->flush();

        // Envoi via MailService
        try {
            $mailService->send(
                to: $user->getEmail(),
                subject: '🎟 Confirmation de votre ticket EvenmoNet',
                htmlContent: "
                <!DOCTYPE html>
                <html lang='fr'>
                <head>
                    <meta charset='UTF-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
                    <style>
                        /* Reset basique */
                        body, p, h1, h2, h3, h4, h5, h6 {
                            margin: 0; padding: 0;
                        }
                        body {
                            background-color: #f7f9fc;
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            color: #333;
                            line-height: 1.6;
                            padding: 20px;
                        }
                        .email-container {
                            max-width: 600px;
                            background-color: #fff;
                            margin: auto;
                            border-radius: 12px;
                            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
                            overflow: hidden;
                            border: 1px solid #ddd;
                        }
                        .header {
                            background: linear-gradient(90deg, #007acc, #00bcd4);
                            padding: 30px 20px;
                            text-align: center;
                            color: white;
                        }
                        .header h1 {
                            font-size: 28px;
                            letter-spacing: 2px;
                            text-transform: uppercase;
                        }
                        .content {
                            padding: 30px 20px;
                        }
                        .content h2 {
                            color: #007acc;
                            margin-bottom: 20px;
                            font-size: 24px;
                        }
                        .ticket-info {
                            background-color: #f0f8ff;
                            border-left: 6px solid #00bcd4;
                            padding: 20px;
                            border-radius: 6px;
                            margin-bottom: 30px;
                            font-size: 16px;
                            color: #004a75;
                        }
                        .ticket-info p {
                            margin-bottom: 12px;
                        }
                        .btn {
                            display: inline-block;
                            background-color: #007acc;
                            color: white !important;
                            padding: 14px 30px;
                            text-decoration: none;
                            border-radius: 30px;
                            font-weight: 700;
                            box-shadow: 0 4px 10px rgba(0, 188, 212, 0.5);
                            transition: background-color 0.3s ease;
                        }
                        .btn:hover {
                            background-color: #005f99;
                        }
                        .footer {
                            font-size: 13px;
                            text-align: center;
                            color: #777;
                            padding: 20px;
                            border-top: 1px solid #eee;
                        }
                        @media (max-width: 480px) {
                            .email-container {
                                width: 100% !important;
                                border-radius: 0;
                                box-shadow: none;
                            }
                            .header h1 {
                                font-size: 22px;
                            }
                            .content h2 {
                                font-size: 20px;
                            }
                            .btn {
                                padding: 12px 20px;
                                font-size: 14px;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='header'>
                            <h1>EvenmoNet</h1>
                        </div>
                        <div class='content'>
                            <h2>Merci pour votre achat ! 🎉</h2>
                            <p>Votre ticket pour l'événement <strong>{$annonce->getTitre()}</strong> a bien été confirmé.</p>
                            <div class='ticket-info'>
                                <p><strong>Prix :</strong> {$annonce->getPrix()} DT</p>
                                <p><strong>Ville :</strong> {$annonce->getVille()}</p>
                                <p><strong>Date d'achat :</strong> " . $ticket->getPurchasedAt()->format('d/m/Y H:i') . "</p>
                            </div>
                            <p>Nous avons hâte de vous voir lors de cet événement exceptionnel.</p>
                            <p style='text-align: center; margin-top: 30px;'>
                                <a href='https://evenmonet.example.com/mes-tickets' class='btn'>Voir mes tickets</a>
                            </p>
                        </div>
                        <div class='footer'>
                            <p>© " . date('Y') . " EvenmoNet. Tous droits réservés.</p>
                            <p>Si vous n'êtes pas à l'origine de cet achat, veuillez contacter notre support.</p>
                        </div>
                    </div>
                </body>
                </html>
                ",
                textContent: "Merci pour votre achat !\nVotre ticket pour l'événement : {$annonce->getTitre()} a bien été confirmé.\nPrix : {$annonce->getPrix()} DT\nVille : {$annonce->getVille()}\nDate d'achat : " . $ticket->getPurchasedAt()->format('d/m/Y H:i') . "\n\nMerci de votre confiance !"
            );
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi du mail : ' . $e->getMessage());
        }

        $this->addFlash('success', 'Ticket acheté avec succès. Vérifiez votre e-mail.');

        return $this->redirectToRoute('app_evenements');
    }

    #[Route('/{id}', name: 'app_annonce_show', methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }
}
