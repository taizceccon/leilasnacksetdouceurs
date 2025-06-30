<?php

namespace App\Controller\Client;

use App\Form\ClientProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/client/profile/edit', name: 'client_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $client = $this->getUser();
        $form = $this->createForm(ClientProfileType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('client_profile_edit');
        }

        return $this->render('client/profile_edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
}