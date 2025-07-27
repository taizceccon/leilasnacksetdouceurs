<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCategoryGettersAndSetters(): void
    {
        $category = new Category();

        // Test du nom de la catégorie
        $name = 'Snacks';
        $category->setCategory($name);
        $this->assertSame($name, $category->getName());

        // Vérifie que les produits sont initialement vides
        $this->assertCount(0, $category->getProducts());
    }
}