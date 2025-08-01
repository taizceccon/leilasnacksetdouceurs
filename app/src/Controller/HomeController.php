<?php

namespace App\Controller;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
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
    // #[Route('/', name: 'app_home')]
    // public function index(Request $request, ProductRepository $productRepository): Response
    // {
    //     $query = $request->query->get('q');
    //     $isSearch = false;
    //     $searchResults = [];

    //     if ($query) {
    //         $isSearch = true;
    //         $searchResults = $productRepository->createQueryBuilder('p')
    //             ->leftJoin('p.category', 'c')
    //             ->addSelect('c')
    //             ->where('p.titre LIKE :q OR p.description LIKE :q')
    //             ->setParameter('q', '%' . $query . '%')
    //             ->getQuery()
    //             ->getResult();
    //     }

    //     $snacks = !$isSearch ? $productRepository->findByCategoryName('Snacks') : [];
    //     $douceurs = !$isSearch ? $productRepository->findByCategoryName('Douceurs') : [];
    //     $packs = !$isSearch ? $productRepository->findByCategoryName('Packs & Coffrets') : [];

    //     return $this->render('home/index.html.twig', [
    //         'isSearch' => $isSearch,
    //         'searchResults' => $searchResults,
    //         'Snacks' => $snacks,
    //         'Douceurs' => $douceurs,
    //         'Packs' => $packs,
    //     ]);
    // }

     #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q');
        $isSearch = false;
        $searchResults = [];

        if ($query) {
            $isSearch = true;
            $searchResults = $productRepository->searchByKeyword($query);
        }

        // Remplace les snacks/douceurs/packs par une sélection aléatoire
        $randomProducts = !$isSearch ? $productRepository->findAllRandom(9) : [];

        return $this->render('home/index.html.twig', [
            'isSearch' => $isSearch,
            'searchResults' => $searchResults,
            'randomProducts' => $randomProducts,
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

        return $this->render('detail.html.twig', [
            'product' => $product,
        ]);
    }


    #[Route('/snacks', name: 'category_snacks')]
    public function showSnacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Snacks']);
       
        if (!$category || $category->getProducts()->isEmpty()) {
            $this->addFlash('warning', 'Il n\'y a pas de produits dans la catégorie Snacks.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('snacks.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/douceurs', name: 'category_douceurs')]
    public function showDouceurs(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Douceurs']);
        if (!$category || $category->getProducts()->isEmpty()) {
            $this->addFlash('warning', 'Il n\'y a pas de produits dans la catégorie Douceurs.');
            return $this->redirectToRoute('app_home');
        }
        return $this->render('douceurs.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/packs-coffrets', name: 'category_packs')]
    public function showPacks(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['category' => 'Packs & Coffrets']);
        if (!$category || $category->getProducts()->isEmpty()) {
           $this->addFlash('warning', 'Il n\'y a pas de produits dans la catégorie Packs & Coffrets.');
           return $this->redirectToRoute('app_home');
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
                ->to('taizceccon@gmail.com')
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
        return $this->render('show.html.twig', [
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

#[Route('/mail-test', name: 'mail_test')]
    public function sendTestMail(MailerInterface $mailer)
    {
       $email = (new Email())
            ->from('hello@example.com')
            ->to('someone@example.com')
            ->subject('Test Mailpit')
            ->text('Ceci est un test')
            ->html('<p>Ceci est un <strong>test</strong> Mailpit.</p>');

        try {
            $mailer->send($email);
            return $this->json(['status' => 'Email envoyé avec succès !']);
        } catch (TransportExceptionInterface $e) {
            return $this->json([
                'status' => 'Erreur lors de l\'envoi de l\'email',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}