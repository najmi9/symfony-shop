<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use PayPalHttp\HttpException;

class PaymentFailedException extends \Exception
{
    public static function fromPaypalHttpException(HttpException $exception): self
    {
        $data = json_decode($exception->getMessage(), true);

        return new self($data['details'][0]['description'] ?? $exception->getMessage());
    }
}
