<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/orders')]
class OrderController extends AbstractController
{
    // Afficher les commandes de l'utilisateur connecté
    #[Route('', name: 'order_index')]
    #[IsGranted('ROLE_USER')] // Seul un utilisateur connecté peut voir ses commandes
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        // Récupérer toutes les commandes de l'utilisateur connecté
        $orders = $em->getRepository(Order::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    // Afficher une commande spécifique (uniquement si elle appartient à l'utilisateur connecté ou un admin)
    #[Route('/{id}', name: 'order_show')]
    #[IsGranted('ROLE_USER')] // Seul un utilisateur connecté peut voir ses commandes
    public function show(Order $order): Response
    {
        // Vérifier que la commande appartient bien à l'utilisateur connecté ou à un admin
        if ($order->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette commande.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/admin/orders', name: 'admin_order_index')]
    #[IsGranted('ROLE_ADMIN')] // Seul un admin peut voir toutes les commandes
    public function adminIndex(EntityManagerInterface $em): Response
    {
        // Récupérer toutes les commandes
        $orders = $em->getRepository(Order::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

}