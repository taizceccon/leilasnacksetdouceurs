<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        // Vérifie que la page répond avec succès (HTTP 200)
        $this->assertResponseIsSuccessful();

        // Vérifie la présence de l'image du logo
        $this->assertSelectorExists('img[alt="Logo Leila Snacks et Douceurs"]');

        // Vérifie la présence du champ email (attention au name="email")
        $this->assertSelectorExists('input[name="email"]');

        // Vérifie la présence du champ mot de passe
        $this->assertSelectorExists('input[name="_password"]');

        // Vérifie la présence du bouton de connexion
        $this->assertSelectorExists('button[type="submit"]');

        // Vérifie la présence du lien d'inscription
        $this->assertSelectorExists('a[href="/register"]');
    }
}