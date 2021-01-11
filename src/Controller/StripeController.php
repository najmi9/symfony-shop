<?php

namespace App\Controller;

use App\Service\Stripe\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;

/**
 * stripe controller
 * 
 * @IsGranted("ROLE_USER")
 */
class StripeController extends AbstractController
{
    /**
     * @Route("/stripe/cretae-session", name="create_checkout_session", methods={"POST"})
     */
    public function pay(StripeService $stripe, SessionInterface $session, 
    ProductRepository $productRepo, EntityManagerInterface $em, CartService $cartService): JsonResponse
    {
        extract($cartService->getData());
        try {
            /** @var Session $stripeSession */
            $stripeSession = $stripe->createSession($total, $images);

            $session->clear('cart');
            $user = $this->getUser();
            $user->setCart([]);
            $em->persist($user);
            $em->flush();

        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->json(['error' => 'Error Found.'], 500);
        }

        return $this->json(['id' => $stripeSession->id], 200);
    }

    /**
     * sfter stripe payment successed
     *
     * @Route("/stripe/success", name="stripe_success", methods={"GET"})
     */
    public function stripeSuccess(): Response
    {
        $this->addFlash('success', 'Payment Successed.');
        return $this->redirectToRoute('cart');
    }

    /**
     * afetr stripe payment canceled
     *
     * @Route("/stripe/cancel", name="stripe_cancel", methods={"GET"})
     */
    public function stripeCanacel(Request $request): Response
    {
        $this->addFlash('warning', 'Payment Canceled.');
        return $this->redirectToRoute('cart');
    }
}
