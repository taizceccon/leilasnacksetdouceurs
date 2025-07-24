<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageContent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/'); 

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Snacks & Douceurs de Leila');
        // $this->assertSelectorExists('.bg-snack');
    }
}