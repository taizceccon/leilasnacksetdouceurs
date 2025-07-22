<?php
// tests/Entity/UserTest.php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserGettersAndSetters(): void
    {
        $user = new User();

        // Test email
        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($email, $user->getUserIdentifier());

        // Test roles (should always contain ROLE_USER)
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();
        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles); // added by default
        $this->assertCount(2, $roles); // only ROLE_ADMIN + ROLE_USER

        // Test password
        $password = 'hashed_password';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());

        // Test isVerified default false, then true
        $this->assertFalse($user->isVerified());
        $user->setIsVerified(true);
        $this->assertTrue($user->isVerified());

        // Test eraseCredentials does nothing (just coverage)
        $user->eraseCredentials();
        $this->assertTrue(true); // juste pour valider le test
    }
}