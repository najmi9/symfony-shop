<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/orders", name="admin_orders_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(OrderRepository $orderRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $orders = $paginator->paginate($orderRepo->findAll(), $request->query->getInt('page', 1), 15);

        return $this->render('admin/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}