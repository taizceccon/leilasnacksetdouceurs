<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class AdminControllerTest extends WebTestCase
{
    public function testAdminIndexPageIsSuccessful(): void
{
    $client = static::createClient();

    // Récupérer un utilisateur valide
    $user = self::getContainer()->get(\App\Repository\UserRepository::class)->findOneBy([]);

    $this->assertNotNull($user, 'Il faut un utilisateur en base pour tester le login.');

    $client->loginUser($user);

    $crawler = $client->request('GET', '/admin');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', 'Tableau de bord'); // adapte selon ton contenu
}
    
}