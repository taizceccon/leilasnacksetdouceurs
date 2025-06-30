<?php

namespace App\Controller\Admin\Client;

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
    public function register(Request $req, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientRegistrationFormType::class, $client);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->setPassword($hasher->hashPassword($client, $client->getPassword()));
            $client->setCreatedAt(new \DateTime());
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_login');
        }

        return $this->render('client/register.html.twig', ['registrationForm' => $form->createView()]);
    }

    #[Route('/client/login', name: 'client_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client/security/login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/client/logout', name: 'client_logout')]
    public function logout(): void { throw new \Exception('Déconnexion gérée par le firewall.'); }

    #[Route('/client', name: 'client_dashboard')]
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        return $this->render('client/dashboard.html.twig');
    }
}