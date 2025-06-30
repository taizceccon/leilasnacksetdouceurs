<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/client/orders', name: 'client_orders')]
    public function index(): Response
    {
        $client = $this->getUser();

        // Récupérer les commandes en BDD associées à ce client ici
        // Exemple: $orders = $orderRepository->findByClient($client);

        return $this->render('client/orders.html.twig', [
            'orders' => [], // remplace par tes commandes réelles
        ]);
    }
}