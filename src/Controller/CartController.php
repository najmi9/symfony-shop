<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add-product", name="cart_add", methods={"POST"})
     */
    public function add(Request $request, SessionInterface $session): Response
    {
        // get the id of the product to add it to the cart
        $id = json_decode($request->getContent(), true);

        $cart = $session->get('cart', []);
        // if this this product is already in the cart we increment the quantity 
        // else we create a new one in the cart
        if ($id) {
            if (!empty($cart[$id])) {
                $cart[$id] +=  1; 
            }else{
                $cart[$id] = 1;
            }
            $session->set('cart', $cart);
        }

        // update the value of the number of product in the cart icon (in the navbar)
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
    public function cart(string $id = null, string $product=null, SessionInterface $session, ProductRepository $productRepo, EntityManagerInterface $em, Request $request): Response
    {   
        // get the cart from the session
        $cart = $session->get('cart', []);

        // buy now a product
        if ($request->query->get('order', false)) {
           $productId = $request->query->get('id');
           $cart[$productId] = 1;
        }

        
        // add a product to the cart
        if ($product) {
            if (!empty($cart[$product])) {
                $cart[$product] +=  1; 
            }else{
                $cart[$product] = 1;
            }
        }

        // delete a product from the cart
        if ($id) {
            $user = $this->getUser();
            unset($cart[$id]);
            if ($user) {
                $user->setCart($cart);
                $em->persist($user);
                $em->flush();
            }
        }

        // update the cart session by the newest one
        $session->set('cart', $cart);

        // fetch products by thier ids
        $products = $productRepo->findProductsById(array_keys($cart));

        // calculate the total price of products
        $total = 0;
        foreach ($products as $product) {
            $total += $product->getPrice() * $cart[$product->getId()];
        }
        
        return $this->render('cart/cart.html.twig', [
            'products' => $products,
            'cart' => $cart,
            'total' => $total,
            'public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }
}
