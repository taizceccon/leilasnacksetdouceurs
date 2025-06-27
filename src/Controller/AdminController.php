<?php 

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        // On récupère les champs de recherche dans la query string
        $search = $request->query->get('search'); // Pour les catégories
        $productSearch = $request->query->get('product_search'); // Pour les produits

        // Recherche des catégories
        $categories = $search
            ? $categoryRepository->createQueryBuilder('c')
                ->where('c.category LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult()
            : $categoryRepository->findAll();

        // Recherche des produits
        $products = $productSearch
            ? $productRepository->createQueryBuilder('p')
                ->where('p.titre LIKE :productSearch')
                ->setParameter('productSearch', '%' . $productSearch . '%')
                ->getQuery()
                ->getResult()
            : $productRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}