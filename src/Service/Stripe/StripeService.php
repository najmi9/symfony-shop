<?php

declare(strict_types=1);

namespace App\Service\Stripe;

use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Checkout\Session;

class StripeService
{
    protected UrlGeneratorInterface $urlGenerator;
    protected string $stripe_secret_key;

    public function __construct(UrlGeneratorInterface $urlGenerator, string $stripe_secret_key)
    {
        $this->urlGenerator = $urlGenerator;
        $this->stripe_secret_key = $stripe_secret_key;
    }

    public function createSession(float $total, array $images)
    {
        Stripe::setApiKey($this->stripe_secret_key);
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $total * 100,
                    'product_data' => [
                        'name' => 'Symfony App',
                        'images' => $images,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('stripe_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        
        return $checkout_session;
    }
}
