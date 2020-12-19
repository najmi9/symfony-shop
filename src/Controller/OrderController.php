<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="orders")
     */
    public function index(OrderRepository $orderRepo, Request $request, PaginatorInterface $paginator, SessionInterface $session): Response
    {
    	$orders = $orderRepo->findByUser($this->getUser());
       // dd($orders);
        $orders = $paginator->paginate(
            $orders, 
            $request->query->getInt('page', 1), /*page number*/
            5
        );
        
        // update the value of the number of product in the cart icon (in the navbar)
        $items = 0;
        foreach ($session->get('cart', []) as $key => $value) {
            $items += $value;
        }

        return $this->render('order/index.html.twig', compact('orders', 'items'));
    }
}
