<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add-product", name="cart_add", methods={"POST"})
     */
    public function add(Request $request, SessionInterface $session): Response
    {
        $id = json_decode($request->getContent(), true);

        $cart = $session->get('cart', []);

        if ($id) {
            if (!empty($cart[$id])) {
                $cart[$id] +=  1; 
            }else{
                $cart[$id] = 1;
            }
            $session->set('cart', $cart);
        }

        $products = 0;

        foreach ($cart as $key => $value) {
            $products += $value;
        }
        return $this->json([
            'products' => $products
        ], 200);
    }

    /**
     * @Route("/cart", name="cart", methods={"GET"})
     * @Route("/cart/delete-product/{id}", name="cart_delete", methods={"GET"})
     * @Route("/cart/add-product/{product}", name="cart_add_product", methods={"GET"})
     */
    public function cart(string $id = null, string $product=null, SessionInterface $session, ProductRepository $productRepo): Response
    {   
        $cart = $session->get('cart', []);

        if ($product) {
            if (!empty($cart[$product])) {
                $cart[$product] +=  1; 
            }else{
                $cart[$product] = 1;
            }
        }

        if ($id) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        $products = $productRepo->findById(array_keys($cart));
        $total = 0;
        foreach ($products as $product) {
             $total += $product->getPrice() * $cart[$product->getId()];
        }
        $items = 0;
        foreach ($cart as $key => $value) {
            $items += $value;
        }
        
        return $this->render('cart/cart.html.twig', [
            'products' => $products,
            'cart' => $cart,
            'total' => $total,
            'items' => $items,
        ]);
    }
}
