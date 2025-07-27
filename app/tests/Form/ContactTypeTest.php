<?php

namespace App\Tests\Form;

use App\Form\ContactType;
use App\Entity\Contact;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;

class ContactTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'nom' => 'Taiz Lib',
            'email' => 'taiz.lib@example.com',
            'sujet' => 'Test Subject',
            'message' => 'Test Message',
        ];

        $contact = new Contact();
        $form = $this->factory->create(ContactType::class, $contact);
        $form->submit($formData);

        $this->assertTrue($form->isValid());

        $this->assertEquals($formData['nom'], $contact->getNom());
        $this->assertEquals($formData['email'], $contact->getEmail());
        $this->assertEquals($formData['sujet'], $contact->getSujet());
        $this->assertEquals($formData['message'], $contact->getMessage());
    }

    public function testSubmitInvalidData(): void
    {
        $formData = [
            'nom' => '',    // nom obligatoire
            'email' => 'not-an-email',
            'sujet' => '',
            'message' => '',
        ];

        $contact = new Contact();
        $form = $this->factory->create(ContactType::class, $contact);
        $form->submit($formData);

        $this->assertFalse($form->isValid());
    }
}