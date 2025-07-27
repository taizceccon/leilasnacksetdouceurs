<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testContactGettersAndSetters(): void
    {
        $contact = new Contact();

        // Nom
        $nom = 'John Doe';
        $contact->setNom($nom);
        $this->assertSame($nom, $contact->getNom());

        // Email
        $email = 'john@example.com';
        $contact->setEmail($email);
        $this->assertSame($email, $contact->getEmail());

        // Sujet
        $sujet = 'Commande';
        $contact->setSujet($sujet);
        $this->assertSame($sujet, $contact->getSujet());

        // Message
        $message = 'Ceci est un message de test.';
        $contact->setMessage($message);
        $this->assertSame($message, $contact->getMessage());

        // CreatedAt
        $date = new \DateTimeImmutable('2025-01-01');
        $contact->setCreatedAt($date);
        $this->assertSame($date, $contact->getCreatedAt());
    }
}