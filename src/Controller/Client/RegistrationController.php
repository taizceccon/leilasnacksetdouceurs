<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Form\ClientRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\ClientAuthenticator;

class RegistrationController extends AbstractController
{
   #[Route('/client/register', name: 'client_register')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        ClientAuthenticator $authenticator
    ): Response {
        $client = new Client();
        $form = $this->createForm(ClientRegistrationFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $client->setPassword(
                $passwordHasher->hashPassword(
                    $client,
                    $form->get('password')->getData()
                )
            );

            // Attribuer le rôle ROLE_CLIENT
            $client->setRoles(['ROLE_CLIENT']);

            // Enregistrer en base
            $entityManager->persist($client);
            $entityManager->flush();

            // Authentifier automatiquement et rediriger
            return $userAuthenticator->authenticateUser(
                $client,
                $authenticator,
                $request
            );
        }

        return $this->render('client/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}