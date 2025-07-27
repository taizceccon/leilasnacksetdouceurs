<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testOrderItemGettersAndSetters(): void
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrix')->willReturn(20); // <-- int, pas float

        $order = $this->createMock(Order::class);

        $orderItem = new OrderItem();
        $orderItem->setItem('Pizza')
                  ->setOrder($order)
                  ->setProduct($product)
                  ->setQuantity(3);

        $this->assertSame('Pizza', $orderItem->getItem());
        $this->assertSame($order, $orderItem->getOrder());
        $this->assertSame($product, $orderItem->getProduct());
        $this->assertSame(3, $orderItem->getQuantity());
        $this->assertSame(60.0, $orderItem->getSubtotal()); // 3 x 20
    }
}