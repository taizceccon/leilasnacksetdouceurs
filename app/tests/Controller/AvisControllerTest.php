<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AvisControllerTest extends WebTestCase
{
    public function getBlockPrefix(): string
    {
        return 'avis';
    }
    public function testAddAvis(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/add-avis');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // Le bouton a pour label "Envoyer" défini dans AvisType
        $form = $crawler->selectButton('Envoyer')->form();

        // Champs sans préfixe, car pas de block_prefix
        $form['avis[nom]'] = 'Joanna Miguel';
        $form['avis[email]'] = 'joanna.miguel@example.com';
        $form['avis[message]'] = 'Ceci est un avis de test'; 
        // $form['message'] = 'Ceci est un avis de test';

        $client->submit($form);

        // Redirection vers la liste après ajout
        $this->assertResponseRedirects('/avis');
        $crawler = $client->followRedirect();

        // Message flash
        $this->assertSelectorExists('.flash-success');

        // Le contenu du message est bien affiché
        $this->assertSelectorTextContains('body', 'Ceci est un avis de test');
    }

    public function testListAvis(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/avis');

        $this->assertResponseIsSuccessful();

        // Vérifie que le titre est présent
        $this->assertSelectorTextContains('h1', 'Avis des gourmands');

        // Vérifie qu’au moins un avis est affiché (si déjà en base)
        $this->assertSelectorExists('.avis-item'); 
    }
}