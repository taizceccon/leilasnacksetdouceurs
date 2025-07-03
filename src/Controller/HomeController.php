<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Category;
use App\Form\CategoryForm;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q');
        $isSearch = false;
        $searchResults = [];

        if ($query) {
            $isSearch = true;
            $searchResults = $productRepository->createQueryBuilder('p')
                ->leftJoin('p.category', 'c')
                ->addSelect('c')
                ->where('p.titre LIKE :q OR p.description LIKE :q')
                ->setParameter('q', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        }

        $snacks = !$isSearch ? $productRepository->findByCategoryName('Snacks') : [];
        $douceurs = !$isSearch ? $productRepository->findByCategoryName('Douceurs') : [];
        $packs = !$isSearch ? $productRepository->findByCategoryName('Packs & Coffrets') : [];

        return $this->render('home/index.html.twig', [
            'isSearch' => $isSearch,
            'searchResults' => $searchResults,
            'Snacks' => $snacks,
            'Douceurs' => $douceurs,
            'Packs' => $packs,
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


    #[Route('/snacks', name: 'category_snacks')]
    public function showSnacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Snacks']);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie Snacks non trouvée.');
        }
        return $this->render('snacks.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/douceurs', name: 'category_douceurs')]
    public function showDouceurs(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Douceurs']);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie Douceurs non trouvée.');
        }
        return $this->render('douceurs.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/packs-coffrets', name: 'category_packs')]
    public function showPacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Packs & Coffrets']);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie Packs & Coffrets non trouvée.');
        }
        return $this->render('packs_coffrets.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $em->persist($contact);
            $em->flush();

            $email = (new Email())
                ->from($contact->getEmail())
                ->to('taizceccon@hotmail.fr')
                ->subject($contact->getSujet())
                ->text($contact->getMessage());

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }



    #[Route('/produit/{id}', name: 'product_show')]
    public function showProduct(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    
    #[Route('category/{id}', name: 'app_category_show', methods: ['GET'])]
    public function showCategory(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }


}