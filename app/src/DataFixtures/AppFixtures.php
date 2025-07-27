<?php

namespace App\DataFixtures;


use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

     // Injecte le service de hashage
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {   
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // User
        $user = new User();
        $user->setEmail('test@test.com');
        $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $manager->persist($user);

        // Creer plusieurs Categories
        $categoryNames = ['Snacks', 'Douceurs', 'Packs & Coffrets'];
        $categories = [];

        foreach ($categoryNames as $name){        
            $category = new Category();
            $category->setCategory($name);
            $manager->persist($category);
            $categories[] = $category;
        }
        // Products avec category aleatoire        
        for ($i = 0; $i< 20; $i++){        
            $product = new Product();
            $product->setTitre('product '.$i);
            $product->setDescription('test description'.$i);
            $product->setPrix(mt_rand(500, 2500));
            $product->setImage("test.webp");  
            $product->setUrlvideo("http://www.youtube.com/v=product.$i"); 
            
            $randomCategory = $categories[array_rand($categories)];
            $product->setCategory($randomCategory);
        
            $manager->persist($product);
        }
    

        $manager->flush();
    }
}