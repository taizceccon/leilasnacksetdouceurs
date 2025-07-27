<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductGettersAndSetters(): void
    {
        $product = new Product();

        // Titre
        $titre = 'Chips Bio';
        $product->setTitre($titre);
        $this->assertSame($titre, $product->getTitre());

        // Description
        $description = 'Délicieuses chips artisanales';
        $product->setDescription($description);
        $this->assertSame($description, $product->getDescription());

        // Prix
        $prix = 250;
        $product->setPrix($prix);
        $this->assertSame($prix, $product->getPrix());

        // Image
        $image = 'chips.jpg';
        $product->setImage($image);
        $this->assertSame($image, $product->getImage());

        // URL vidéo
        $urlvideo = 'https://youtube.com/snack-video';
        $product->setUrlvideo($urlvideo);
        $this->assertSame($urlvideo, $product->getUrlvideo());

        // Category
        $category = new Category();
        $product->setCategory($category);
        $this->assertSame($category, $product->getCategory());
    }

    public function testAddAndRemoveOrderItem(): void
    {
        $product = new Product();
        $orderItem = new OrderItem();

        $product->addOrderItem($orderItem);
        $this->assertCount(1, $product->getOrderItems());
        $this->assertSame($product, $orderItem->getProduct());

        $product->removeOrderItem($orderItem);
        $this->assertCount(0, $product->getOrderItems());
        $this->assertNull($orderItem->getProduct());
    }
}