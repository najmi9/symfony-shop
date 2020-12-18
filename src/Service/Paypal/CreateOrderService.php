<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class CreateOrderService
{
    /**
     * Setting up the JSON request body for creating the Order. The Intent in the
     * request body should be set as "CAPTURE" for capture intent flow.
     */
    public function buildRequestBody(
            array $items,
            string $subtotal,  
            string $total,
            string $shippingPrice,
            string $handlingPrice,
            string $currency,
            string $address,
            string $description
    )
    {
        return [
            'intent' => 'CAPTURE',
            'application_context' =>
                [
                    'brand_name' => 'EXAMPLE INC',
                    'locale' => 'en-US',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ],
            'purchase_units' =>[
                ['amount' =>[
                    'currency_code' => $currency,
                    'value' => $total,
                    'breakdown' =>[
                        'item_total' =>[
                                'currency_code' => $currency,
                                'value' => $subtotal,
                            ],
                        'shipping' =>
                            [
                                'currency_code' => $currency,
                                'value' => $shippingPrice,
                            ],
                        'handling' =>
                            [
                                'currency_code' => $currency,
                                'value' =>  $handlingPrice,
                            ],
                    ],
                ],
                'description' => $description,
                'custom_id' => 'CUST-HighFashions',
                'soft_descriptor' => 'HighFashions',
                'items' => $items,

            ]],
        ];
    }

    /**
     * This is the sample function which can be sued to create an order. It uses the
     * JSON body returned by buildRequestBody() to create an new Order.
     */
    public function createOrder(PayPalHttpClient $client, array $body)
    {
        $request = new OrdersCreateRequest();
        $request->headers["prefer"] = "return=representation";
        $request->body = $body;

        /** @var mixed */
        $response = $client->execute($request);
        return $response;
    }
}
