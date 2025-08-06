<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;


    #[Route('/stripe')]
    final class StripeController extends AbstractController
    {
        #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
        public function addToCart(Request $request, CartService $cartService): Response
        {
            $productId = (int) $request->request->get('product_id');
            $quantity = max(1, (int) $request->request->get('quantity', 1));
            $cartService->add($productId, $quantity);

            return $this->redirectToRoute('app_stripe_test');
        }

    // #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    // public function updateCart(Request $request, int $id, CartService $cartService): JsonResponse
    // {
    //     $quantity = (int) $request->request->get('quantity');
        
    //     if ($quantity < 1) {
    //         return new JsonResponse(['success' => false, 'message' => 'Quantité invalide.'], 400);
    //     }

    //     $cartService->update($id, $quantity);

    //     $cart = $cartService->getCart();

    //     $subtotal = 0;
    //     foreach ($cart as $item) {
    //         if ($item['product']->getId() === $id) {
    //             $subtotal = $item['product']->getPrix() * $item['quantity'];
    //             break;
    //         }
    //     }

    //     $total = $cartService->getTotal();

    //     return new JsonResponse([
    //         'success' => true,
    //         'subtotal' => $subtotal,
    //         'total' => $total,
    //     ]);
    // }


    #[Route('/test', name: 'app_stripe_test')]
    public function stripeTest(ProductRepository $productRepository, CartService $cartService): Response
    {
        $products = $productRepository->findAll();
        $cartItems = $cartService->getCart();
        $cartTotal = $cartService->getTotal();

        return $this->render('stripe/test.html.twig', [
            'products' => $products,
            'cart' => $cartItems,
            'total' => $cartTotal,
        ]);
    }

    #[Route('/test/checkout/{id}', name: 'stripe_test_checkout')]
    public function stripeTestCheckout(Product $product): Response
    {
        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getTitre(),
                    ],
                    'unit_amount' => $product->getPrix(),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($checkout_session->url);
    }

    #[Route('/checkout', name: 'checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(CartService $cartService, EntityManagerInterface $em): Response
    {
        Stripe::setApiKey($this->getParameter('stripe.secret_key'));

        $cart = $cartService->getCart();
        $lineItems = [];

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $order->setStatus('En vérification');

        foreach ($cart as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getTitre(),
                    ],
                    'unit_amount' => $product->getPrix(),
                ],
                'quantity' => $quantity,
            ];

            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setUnitPrice($product->getPrix());
            // $orderItem->setSubtotal($product->getPrix() * $quantity);

            $order->addItem($orderItem);

            $em->persist($orderItem);
        }

        $order->recalculateTotal();// recalcul du total après ajout des items

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $em->persist($order);
        $em->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(CartService $cartService): Response
    {
        $cartService->clear();
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }

    #[Route('/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function stripeWebhook(Request $request, EntityManagerInterface $em): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = $this->getParameter('stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $order = $em->getRepository(Order::class)->findOneBy([
                'stripeSessionId' => $session->id,
            ]);

            if ($order) {
                $order->setStatus('paid');
                $em->flush();
            }
        }

        return new Response('Webhook processed', 200);
    }
}