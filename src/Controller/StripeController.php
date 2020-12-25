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
use App\Service\ProjectConstants;
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
    public function pay(StripeService $stripe, SessionInterface $session, ProductRepository $productRepo, EntityManagerInterface $em): JsonResponse
    {
        $cart = $session->get('cart', []);

        $products = $productRepo->findProductsById(array_keys($cart));

        $subtotal = 0;
        $images = [];

        foreach ($products as $product) {
            $subtotal += round($product->getPrice()) * $cart[$product->getId()->__toString()];
            $images[] = $product->getImage();
        }

        $shippingPrice = ProjectConstants::SHIPPING_PRICE;
        $currency = ProjectConstants::CURRENCY;
        $handlingPrice = ProjectConstants::HANDLINH_PRICE;
        $total = $subtotal + $shippingPrice + $handlingPrice;
        try {
            /** @var Session $session */
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
    public function stripeSuccess(Request $request): Response
    {
        dd($request);
    }

    /**
     * afetr stripe payment canceled
     *
     * @Route("/stripe/cancel", name="stripe_cancel", methods={"GET"})
     */
    public function stripeCanacel(Request $request): Response
    {
        dd($request);
    }
}
