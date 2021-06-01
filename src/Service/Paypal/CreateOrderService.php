<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpRequest;
use PayPalHttp\HttpResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CreateOrderService
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Setting up the JSON request body for creating the Order. The Intent in the
     * request body should be set as "CAPTURE" for capture intent flow.
     */
    private function buildRequestBody(Payment $payment): array
    {
        return [
            'intent' => 'CAPTURE',
            'application_context' =>
                [
                    'return_url' => $this->urlGenerator->generate('paypal_return_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'cancel_url' => $this->urlGenerator->generate('paypal_cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    'brand_name' => 'Symfony App with Najmi Imad',
                    'locale' => 'en-US',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ],
            'purchase_units' =>[
                ['amount' =>[
                    'currency_code' => $payment->getCurrency(),
                    'value' => $payment->getTotal(),
                    'breakdown' =>[
                        'item_total' =>[
                                'currency_code' => $payment->getCurrency(),
                                'value' => $payment->getSubTotal(),
                            ],
                        'shipping' =>
                            [
                                'currency_code' => $payment->getCurrency(),
                                'value' => $payment->getShippingPrice(),
                            ],
                        'handling' =>
                            [
                                'currency_code' => $payment->getCurrency(),
                                'value' =>  $payment->getHandlingPrice(),
                            ],
                    ],
                ],
                'description' => $payment->getDescription(),
                'custom_id' => 'CUST-HighFashions',
                'soft_descriptor' => 'HighFashions',
                'items' => $payment->getItems(),
            ]],
        ];
    }

    /**
     * This is the sample function which can be sued to create an order. It uses the
     * JSON body returned by buildRequestBody() to create an new Order.
     */
    public function createOrder(PayPalHttpClient $client, Payment $payment): HttpResponse
    {
        /** @var HttpRequest $request */
        $request = new OrdersCreateRequest();
        $request->headers['prefer'] = 'return=representation';
        $request->body = $this->buildRequestBody($payment);

        return $client->execute($request);
    }
}
