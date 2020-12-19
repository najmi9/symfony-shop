<?php

declare(strict_types=1);

namespace App\tests\Service;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartServiceTest extends WebTestCase
{
    private CartService $cartService;

    public function setUp(): void
    {
        $client = self::createClient();
        $this->cartService = $client->getContainer()->get(CartService::class);
        parent::setUp();
    }

    /**
     * test that merge to tables give us a table
     * 
     * @test
     */
    public function mergeCartsAfterLogin()
    {
        $cartUser = [];
        $cartSession = [];

        $key1 = 'prefix' . uniqid();
        $key2 = 'prefix' . uniqid();
        $key3 = 'prefix' . uniqid();

        $cartUser[$key1] = 3; //  [key1 => 3, key2 => 1]
        $cartUser[$key2] = 1;

        $cartSession[$key2] = 1; // [ key2 => 1, key3 => 1]
        $cartSession[$key3] = 1;

        $result = $this->cartService->mergeCartsAfterLogin($cartUser, $cartSession);

        $expectedResult = [
            $key1 => 3,
            $key2 => 2,
            $key3 => 1
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
