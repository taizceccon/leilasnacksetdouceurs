<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductController extends AbstractController
{
    #[Route('/produit/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin', name: 'admin_index')]
    public function index(Request $request, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $search = $request->query->get('search');
        $productSearch = $request->query->get('product_search');

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
            'search' => $search,
            'product_search' => $productSearch
        ]);
    }

    #[Route('/admin/new', name: 'admin_product_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer l'erreur ici, par ex. loguer ou afficher un message
                }

                $product->setImage($newFilename);
            }

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}/modifier', name: 'product_edit')]
    public function edit(Request $request, Product $product, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer l'erreur ici
                }

                $product->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/produit/{id}/supprimer', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_product_' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Produit supprimé avec succès.');
        }

        return $this->redirectToRoute('admin');
    }

}
