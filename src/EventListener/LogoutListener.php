<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LogoutListener
{
    private EntityManagerInterface $em;
    private SessionInterface $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $event)
    {
        /** @var User $user*/
        $user = $event->getToken()->getUser();

        $cart = $this->session->get('cart', []);

        $user->setCart($cart);

        $this->session->clear();
        // Persist the data to database.
        $this->em->persist($user);
        $this->em->flush();
    }
}