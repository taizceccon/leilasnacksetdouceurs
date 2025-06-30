<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        // Sécurité côté contrôleur (double sécurité)
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $products = $productRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}