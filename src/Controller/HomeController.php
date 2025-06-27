<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


final class HomeController extends AbstractController
{
    // #[Route('/', name: 'app_home')]
    // public function index(): Response
    // {
    //     return $this->render('index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }

    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $snacks = $productRepository->findByCategoryName('Snacks');
        $douceurs = $productRepository->findByCategoryName('Douceurs');

        dump($snacks, $douceurs);

        return $this->render('home/index.html.twig', [
            'Snacks' => $snacks,
            'Douceurs' => $douceurs,
        ]);
    }



   #[Route('/products', name: 'products_index')]
    public function products(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllWithProducts();

        return $this->render('product/index.html.twig', [
            'categories' => $categories,
        ]);
    }


    #[Route('/product/{id}', name: 'product_detail')]
    public function productDetail(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function admin(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $products = $productRepository->findAll();
        $categories = $categoryRepository->findAll(); // ✅ Corrigé : renommé en $categories pour cohérence avec Twig

        return $this->render('admin/index.html.twig', [
            'products' => $products,
            'categories' => $categories, // ✅ Ce nom doit correspondre à celui utilisé dans le template Twig
        ]);
    }
   
    #[Route('/snacks', name: 'category_snacks')]
    public function showSnacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find(1); // ID de la catégorie "snacks"

        return $this->render('product/snacks.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/douceurs', name: 'category_douceurs')]
    public function showDouceurs(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find(2); // ID de la catégorie "Douceurs"

        return $this->render('product/douceurs.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/packs-coffrets', name: 'category_packs')]
    public function showPacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find(3); // ID de la catégorie "Packs & Coffrets"

        return $this->render('product/packs_coffrets.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/commander', name: 'order_index')]
    public function commander(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // traites la commande
        return $this->render('order/index.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTime());
            $em->persist($contact);
            $em->flush();

            // Envoi de l'email (exemple)
            $email = (new \Symfony\Component\Mime\Email())
                ->from($contact->getEmail())
                ->to('tzlogicsolutions@gmail.com')
                ->subject($contact->getSujet())
                ->text($contact->getMessage());

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}