<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show')]
    public function showCart(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        dump($cart);
        $products = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->getPrix() * $quantity,
                ];
                $total += $product->getPrix() * $quantity;
            }
        }

        return $this->render('cart/show.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
    public function addCart(Request $request, SessionInterface $session): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));

        // Déboguer l'état du panier
        dump($session->get('cart'));  

        $cart = $session->get('cart', []);
        $cart[$productId] = isset($cart[$productId]) ? $cart[$productId] + $quantity : $quantity;

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(Request $request, SessionInterface $session, $id): Response
    {
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id] = $quantity;
            $session->set('cart', $cart);
            $this->addFlash('success', 'Quantité mise à jour.');
        } else {
            $this->addFlash('error', 'Produit non trouvé dans le panier.');
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeCart(string $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);
            $this->addFlash('success', 'Produit supprimé du panier.');
        }

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clearCart(SessionInterface $session): Response
    {
        $session->remove('cart');
        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/commander', name: 'order_create')]
    public function createOrder(SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if (!$user instanceof User || !$user->getEmail()) {
            $this->addFlash('error', 'Utilisateur non valide ou email introuvable.');
            return $this->redirectToRoute('cart_show');
        }

        // Récupérer le panier
        $cart = $session->get('cart', []);
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('cart_show');
        }

        // Créer la nouvelle commande
        $newOrder = new Order();
        $newOrder->setUser($user);
        $newOrder->setCreatedAt(new \DateTimeImmutable());
        $newOrder->setStatus('en attente'); // Par exemple

        // Calculer le total de la commande
        $total = 0;
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setSubtotal($product->getPrix() * $quantity);

                $newOrder->addItem($orderItem);
                $total += $orderItem->getSubtotal();
            } else {
                // Si le produit n'est pas trouvé, renvoie une erreur ou continue
                $this->addFlash('error', "Le produit avec l'ID $productId n'existe plus.");
                return $this->redirectToRoute('cart_show');
            }
        }

        $newOrder->setTotal($total);

        // Persister la commande et ses items
        $em->persist($newOrder);
        $em->flush();

        // Vider le panier
        $session->remove('cart');

        // Rediriger vers la page de la commande
        return $this->redirectToRoute('order_show', ['id' => $newOrder->getId()]);
    }

    #[Route('/orders', name: 'order_index')]
    public function index(OrderRepository $orderRepository)
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos commandes.');
            return $this->redirectToRoute('cart_show');
        }

        // Récupérer toutes les commandes liées à l'utilisateur connecté
        $orders = $orderRepository->findBy(['user' => $user]);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}', name: 'order_show')]
    public function show(OrderRepository $orderRepository, int $id): Response
    {
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order, // Passez 'order', pas 'orders'
        ]);
    }
}