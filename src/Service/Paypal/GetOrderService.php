<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class GetOrderService
{
    /**
     * This function can be used to retrieve an order by passing order Id as argument.
     */
    public  function getOrder(PayPalHttpClient $client, string $orderId)
    {
        /** @var mixed */
        $response = $client->execute(new OrdersGetRequest($orderId));
   
        return $response;
    }
}
