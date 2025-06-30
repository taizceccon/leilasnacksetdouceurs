<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ClientController extends AbstractController
{
    #[Route('/client/register', name: 'client_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientRegistrationFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setPassword(
                $passwordHasher->hashPassword($client, $client->getPassword())
            );
            $client->setRoles(['ROLE_CLIENT']);
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('client_login');
        }

        return $this->render('client/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/client/login', name: 'client_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
{
    if ($this->getUser()) {
        dump($this->getUser()); // Voir si un utilisateur est retourné
        die("teste");
        return $this->redirectToRoute('client_dashboard');
    }

    return $this->render('client/login.html.twig', [
        'last_username' => $authenticationUtils->getLastUsername(),
        'error' => $authenticationUtils->getLastAuthenticationError(),
    ]);
}

    #[Route('/client/logout', name: 'client_logout')]
    public function logout(): void
    {
        // Symfony gère la déconnexion automatiquement
        throw new \Exception('Cette méthode peut rester vide - elle sera interceptée par la clé logout dans votre firewall.');
    }

    #[Route('/client', name: 'client_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        return $this->render('client/dashboard.html.twig');
    }
}