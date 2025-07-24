<?php 

namespace App\Tests\Form;

use App\Form\ContactType;
use App\Entity\Contact;
use Symfony\Component\Form\Test\TypeTestCase;

class ContactTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'nom' => 'John Doe',
            'email' => 'john.doe@example.com',
            'sujet' => 'Test Subject',
            'message' => 'Test Message',
        ];

        $contact = new Contact();
        $form = $this->factory->create(ContactType::class, $contact);
        $form->submit($formData);

        // Assert that the form is valid
        $this->assertTrue($form->isValid());
        // Assert that the entity was populated with the submitted data
        $this->assertEquals($formData['nom'], $contact->getNom());
        $this->assertEquals($formData['email'], $contact->getEmail());
        $this->assertEquals($formData['sujet'], $contact->getSujet());
        $this->assertEquals($formData['message'], $contact->getMessage());
    }
}