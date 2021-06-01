<?php

declare(strict_types=1);

namespace App\Service\Paypal;

use App\Entity\Transaction;
use App\Service\Paypal\PaymentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvent::class => 'onPayment',
        ];
    }

    public function onPayment(PaymentEvent $event): void
    {
        // On regarde si le paiement correspond à un plan
        $payment = $event->getPayment();
        $user = $event->getCustomer();
       
        $type = 'paypal'; // can be 2checkout

        // On enregistre la transaction
        $transaction = (new Transaction())
            ->setPrice($payment->amount)
            ->setTax($payment->vat)
            ->setAuthor($event->getCustomer())
            ->setMethod($type)
            ->setFirstname($payment->firstname)
            ->setLastname($payment->lastname)
            ->setCity($payment->city)
            ->setCountryCode($payment->countryCode)
            ->setAddress($payment->address)
            ->setPostalCode($payment->postalCode)
            ->setMethodRef($payment->id)
            ->setFee($payment->fee)
            ->setCreatedAt(new \DateTime());
        $this->em->persist($transaction);
    }
}
