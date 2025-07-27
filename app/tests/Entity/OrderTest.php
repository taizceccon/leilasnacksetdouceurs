<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
   public function testOrderGettersAndSetters(): void
   {
    $order = new Order();

    $user = new User();
    $order->setUser($user);
    $this->assertSame($user, $order->getUser());

    $status = 'paid';
    $order->setStatus($status);
    $this->assertSame($status, $order->getStatus());

    $createdAt = new \DateTimeImmutable('2025-01-01');
    $order->setCreatedAt($createdAt);
    $this->assertSame($createdAt, $order->getCreatedAt());

    $item1 = $this->createMock(\App\Entity\OrderItem::class);
    $item1->method('getSubtotal')->willReturn(100.0);

    $item2 = $this->createMock(\App\Entity\OrderItem::class);
    $item2->method('getSubtotal')->willReturn(200.0);

    $order->addItem($item1);
    $order->addItem($item2);

    $this->assertCount(2, $order->getOrderItems());
    $this->assertEquals(300.0, $order->getTotal());
   }
}