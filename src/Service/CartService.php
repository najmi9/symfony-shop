<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\ProjectConstants;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private ProductRepository $productRepo;

    public function __construct(SessionInterface $session, ProductRepository $productRepo)
    {
        $this->session = $session;
        $this->productRepo = $productRepo;
    }

    /**
     * merge two tables
     *
     * @param array<{string:int}> $cart1 ['key' => 124, 'keyprim' => 1]
     * @param array<{string:int}> $cart2 ['key' => 4, 'key1' => 1]
     * @return array<{string:int}>
     */
    public function mergeCartsAfterLogin(array $cart1, array $cart2): array
    {
        $totalCart = [];

        foreach (array_merge_recursive($cart1, $cart2) as $key => $value) {
            if (is_array($value)) {
               // 
               // [
               //    0 => 2, // value of session
               //    1 => 5  // value of database
               // ]
               //
                $totalCart[$key] = array_sum($value); // $totalCart[$key] = 7;
            }else {
                // 6 value from the database or the session
                $totalCart[$key] = $value; // $totalCart[$key] = 6;
            }
        }
    
        return $totalCart;
    }

    public function getData(): array
    {
        $cart = $this->session->get('cart', []);

        $products = $this->productRepo->findProductsById(array_keys($cart));

        $subtotal = 0;

        $items = [];
        $images = [];

        foreach ($products as $product) {
            $subtotal += round($product->getPrice()) * $cart[$product->getId()->__toString()];
            $items[] = [
                'name' => $product->getName(),
                'description' => $product->getCategory()->getTitle(),
                'quantity' => (string) $cart[$product->getId()->__toString()],
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' =>  (string) round($product->getPrice()),
                ],
                'category' => 'PHYSICAL_GOODS',
            ];

            $images[] = $product->getImage();
        }

        $address = 'SET_PROVIDED_ADDRESS';

        $shippingPrice = ProjectConstants::SHIPPING_PRICE;
        $currency = ProjectConstants::CURRENCY;
        $handlingPrice = ProjectConstants::HANDLINH_PRICE;
        $total = $subtotal + $shippingPrice + $handlingPrice;

        $description = 'DESCRIPTION OF ORDER';

        return [
            'total' => $total,
            'currency' => $currency,
            'items' => $items,
            'images' => $images,
            'handlingPrice' => $handlingPrice,
            'description' => $description,
            'shippingPrice' => $shippingPrice,
            'address' => $address,
            'subtotal' => $subtotal
        ];
    }

}