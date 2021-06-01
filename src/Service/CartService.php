<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ProductRepository;
use App\Service\Paypal\Payment;
use App\Service\ProjectConstants;

class CartService
{
    private ProductRepository $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

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
            } else {
                // 6 value from the database or the session
                $totalCart[$key] = $value; // $totalCart[$key] = 6;
            }
        }
    
        return $totalCart;
    }

    public function generatePayment(array $cart): Payment
    {
        $products = $this->productRepo->findProductsById(array_keys($cart));

        $subtotal = 0;

        $items = [];
        $images = [];

        foreach ($products as $product) {
            $subtotal += round($product->getPrice()) * $cart[$product->getId()];
            $items[] = [
                'name' => $product->getName(),
                'description' => $product->getCategory()->getTitle(),
                'quantity' => (string) $cart[$product->getId()],
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' =>  (string) round($product->getPrice()),
                ],
                'category' => 'PHYSICAL_GOODS',
            ];

            $images[] = $product->getImage();
        }

        $payment = new Payment();


        $shippingPrice = ProjectConstants::SHIPPING_PRICE;
        $handlingPrice = ProjectConstants::HANDLINH_PRICE;

        $total = $subtotal + $shippingPrice + $handlingPrice;

        return $payment->setAddress('SET_PROVIDED_ADDRESS')
            ->setDescription('DESCRIPTION OF ORDER')
            ->setCurrency(ProjectConstants::CURRENCY)
            ->setHandlingPrice($handlingPrice)
            ->setTotal($total)
            ->setSubTotal($subtotal)
            ->setShippingPrice($shippingPrice)
            ->setItems($items)
            ->setImages($images)
        ;
    }
}