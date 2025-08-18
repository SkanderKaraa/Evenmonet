<?php

namespace App\Tests\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Entity\Ticket;

use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AnnonceControllerTest extends WebTestCase
{
    public function testAcheterTicket()
    {
        $client = static::createClient();

        $em = $client->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy([]);
        $this->assertNotNull($user, 'Aucun utilisateur en base pour le test.');

        $client->loginUser($user);


        // Récupérer une annonce existante en base (ou la créer dans le test)
        $em = $client->getContainer()->get('doctrine')->getManager();
        $annonce = $em->getRepository(Annonce::class)->findOneBy([]); // Prend la première annonce

        $this->assertNotNull($annonce, 'Aucune annonce trouvée en base pour le test');

        // Faire une requête POST vers la route d'achat ticket
        $client->request('GET', '/annonce/acheter-ticket/' . $annonce->getId());

        // Vérifier que la réponse est une redirection
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Vérifier que la redirection est vers la page des événements
        $this->assertResponseRedirects('/annonce/evenements');

        // Suivre la redirection pour vérifier le flash message
        $client->followRedirect();

        //$this->assertSelectorExists('.flash-success');
        //$this->assertSelectorTextContains('.flash-success', 'Ticket acheté avec succès');

        // Eventuellement vérifier que le ticket a été créé en base
        $ticket = $em->getRepository(Ticket::class)->findOneBy(['user' => $user, 'annonce' => $annonce]);
        $this->assertNotNull($ticket, 'Le ticket n’a pas été créé.');
    }
}
