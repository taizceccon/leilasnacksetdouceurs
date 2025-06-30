<?php

namespace App\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'client_dashboard')]
    public function index(): Response
    {
        return $this->render('client/dashboard.html.twig');
    }
}