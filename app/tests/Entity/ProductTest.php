<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\OrderItem;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductEntity(): void
    {
        $product = new Product();

        $category = new Category();
        $orderItem1 = new OrderItem();
        $orderItem2 = new OrderItem();

        $product
            ->setTitre('Produit Test')
            ->setDescription('Description de test')
            ->setPrix(1999)
            ->setImage('image.webp')
            ->setUrlvideo('https://example.com/video.mp4')
            ->setCategory($category);

        $this->assertSame('Produit Test', $product->getTitre());
        $this->assertSame('Description de test', $product->getDescription());
        $this->assertSame(1999, $product->getPrix());
        $this->assertSame('image.webp', $product->getImage());
        $this->assertSame('https://example.com/video.mp4', $product->getUrlvideo());
        $this->assertSame($category, $product->getCategory());

        // Test OrderItem relation
        $product->addOrderItem($orderItem1);
        $product->addOrderItem($orderItem2);

        $this->assertCount(2, $product->getOrderItems());
        $this->assertTrue($product->getOrderItems()->contains($orderItem1));
        $this->assertTrue($product->getOrderItems()->contains($orderItem2));

        $product->removeOrderItem($orderItem1);
        $this->assertCount(1, $product->getOrderItems());
        $this->assertFalse($product->getOrderItems()->contains($orderItem1));
    }
}