<?
namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixturesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    // Setup : Avant chaque test, on instancie l'EntityManager et le service de hashage
    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$container->get(EntityManagerInterface::class);
        $this->passwordHasher = self::$container->get(UserPasswordHasherInterface::class);
    }

    public function testFixtures(): void
    {
        // Charge les fixtures
        $fixture = new AppFixtures($this->passwordHasher);
        $fixture->load($this->entityManager);

        // Vérifier que l'utilisateur est bien ajouté
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'test@test.com']);
        $this->assertNotNull($user);
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertTrue($this->passwordHasher->isPasswordValid($user, '123456'));

        // Vérifier que les catégories sont bien insérées
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        $this->assertCount(3, $categories);  // On a créé 3 catégories

        // Vérifier que des produits ont bien été créés et sont associés à des catégories
        $productRepository = $this->entityManager->getRepository(Product::class);
        $products = $productRepository->findAll();
        $this->assertCount(20, $products);  // On a créé 20 produits

        // Vérifier qu'un produit a une catégorie assignée
        $product = $products[0];
        $this->assertNotNull($product->getCategory());
        $this->assertInstanceOf(Category::class, $product->getCategory());
    }
}