<?php
namespace App\Tests;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient(); // Crée un navigateur simulé (client HTTP)
        $crawler = $client->request('GET', '/'); // Fait une requête GET sur la page d'accueil
        $this->assertResponseIsSuccessful(); // Vérifie que la réponse HTTP est 200 (OK)
        $this->assertSelectorTextContains('h1', 'Snacks & Douceurs de Leila'); // Vérifie que le h1 contient le bon texte
    }

     public function testCategorySnackMayExist(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Vérifie que le mot "Snacks" existe quelque part, sans erreur si absent
        $this->assertTrue(
            str_contains($crawler->filter('body')->text(), 'Snacks'),
            'Catégorie "Snacks" non trouvée sur la page.'
        );
    }

    public function testProductFromFixturesIsVisible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertStringContainsString(
            'product 1',
            $crawler->filter('body')->text(),
            'Produit "product 1" non trouvé sur la page.'
        );
    }

   public function testAnyProductLinkIsAccessible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Cherche le premier lien vers un produit, supposé être dans un <a> avec un titre
        $productLinkNode = $crawler->filter('a')->reduce(function ($node) {
            return str_contains(strtolower($node->text()), 'product');
        });

        if ($productLinkNode->count() === 0) {
            $this->fail('Aucun lien vers un produit trouvé sur la page.');
        }

        $client->click($productLinkNode->link());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1'); // ou tout autre sélecteur dans la page produit
    }


}
