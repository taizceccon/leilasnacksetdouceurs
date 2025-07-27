<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageContent()
       {
            $client = static::createClient();
            $crawler = $client->request('GET', '/register');

            $this->assertResponseIsSuccessful();
            $this->assertSelectorExists('form[name=registration_form]');
            // ou vérifier un h1 spécifique à la page d'inscription si tu en as un
    }
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name=registration_form]');
    }

    public function testRegisterNewUser(): void
    {
        $email = 'user_' . uniqid() . '@example.com';

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('S\'inscrire')->form([
            'registration_form[email]' => $email,
            'registration_form[plainPassword]' => '1234563',
            'registration_form[agreeTerms]' => true,
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertSelectorExists('.flash-success');
        $this->assertSelectorTextContains('.flash-success', 'Votre compte a bien été créé');
    }
}