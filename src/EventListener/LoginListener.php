<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class LoginListener
{
    private EntityManagerInterface $em;
    private SessionInterface $session;
    private CartService $cartService;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, CartService $cartService)
    {
        $this->em = $em;
        $this->session = $session;
        $this->cartService = $cartService;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
         /** @var User $user*/
        $user = $event->getAuthenticationToken()->getUser();
        $cart = $this->session->get('cart', []);
        $userCart = $user->getCart() ?? [];

        $totalCart = $this->cartService->mergeCartsAfterLogin($cart, $userCart);

        $user->setCart($totalCart);

        $this->session->set('cart', $totalCart);
        
        $this->em->persist($user);
        $this->em->flush();
    }
}