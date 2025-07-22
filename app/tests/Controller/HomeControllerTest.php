<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageContent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');  // adapte la route si besoin

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Snacks & Douceurs de Leila');
        $this->assertSelectorExists('section.text-center.py-16.bg-[#E5C1BD]');
    }
}