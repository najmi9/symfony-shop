<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use App\Entity\Customer;

class PaymentEvent
{
    private Payment $payment;
    private Customer $customer;

    public function __construct(Payment $payment, Customer $customer)
    {
        $this->payment = $payment;
        $this->customer = $customer;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
