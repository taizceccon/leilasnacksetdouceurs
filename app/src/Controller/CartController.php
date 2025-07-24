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
            // Vérifier que le produit existe
            $product = $productRepository->find($productId);
            if ($product) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setItem($product->getTitre());  // Remplir l'attribut 'item' avec le titre du produit

                $subtotal = $product->getPrix() * $quantity;
                $orderItem->setSubtotal($subtotal);

                // Ajouter l'item à la commande
                $newOrder->addItem($orderItem);
                $total += $subtotal;
            } else {
                // Produit introuvable, on ajoute un message d'erreur et on redirige
                $this->addFlash('error', "Le produit avec l'ID $productId n'existe plus.");
                return $this->redirectToRoute('cart_show');
            }
        }

        // Vérifier qu'au moins un item a été ajouté avant de persister
        if (count($newOrder->getOrderItems()) === 0) {
            $this->addFlash('error', 'Aucun produit valide dans le panier.');
            return $this->redirectToRoute('cart_show');
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
    public function index(OrderRepository $orderRepository): Response
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

    // Route pour afficher une commande spécifique
    #[Route('/orders/{id}', name: 'order_show')]
    public function show(Order $order = null): Response
    {
        // Vérifier si la commande existe, sinon afficher une page d'erreur
        if ($order === null) {
            throw $this->createNotFoundException('Commande non trouvée.');
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
    

    // Route pour lister toutes les commandes
  #[Route('/orders/all', name: 'order_list')]
    public function list(OrderRepository $orderRepository)
    {
        // Récupérer toutes les commandes depuis la base de données
        $orders = $orderRepository->findAll();

        // Rendre la vue avec la liste des commandes
        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    
}