<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_show')]
    public function show(SessionInterface $session, ProductRepository $productRepository): Response
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
                    'subtotal' => (int) $product->getPrix() * $quantity,
                    // 'subtotal' => $product->getPrix() * $quantity,
                ];
                $total += (int) $product->getPrix() * $quantity;
            }
        }

        return $this->render('cart/show.html.twig', [
            'products' => $products,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
    public function add(Request $request, SessionInterface $session): Response
    {
        $productId = $request->request->get('product_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));

        $cart = $session->get('cart', []);

        if (!empty($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(string $id, SessionInterface $session): Response
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
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');
        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/commander', name: 'order_index')]
    public function commander(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Récupérer le panier, l’utilisateur, etc.
        return $this->render('order/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

}
