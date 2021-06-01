<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/orders", name="orders_")
 */
class OrderController extends AbstractController
{
    private ProductRepository $productRepo;
    private OrderRepository $orderRepo;

    public function __construct(ProductRepository $productRepo, OrderRepository $orderRepo)
    {
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
    }

    /**
     * @Route("/all", name="index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $orders = $this->orderRepo->createQueryBuilder('o')
            ->where('o.user = :user')->setParameter('user', $this->getUser())
            ->select('o.id, o.status, o.approveLink, o.createdAt, o.fee, o.amount, o.identifiant')
            ->getQuery()
        ;
        $orders = $paginator->paginate($orders, $request->query->getInt('page', 1), 10);

        return $this->render('order/index.html.twig', compact('orders'));
    }

    /**
     * @Route("/to-be-shipped", name="to_be_shipped", methods={"GET"})
    */
    public function toBeShipped(): Response
    {
        // products that are payed for a user and is completed and ready to be shipped
        $orders = $this->orderRepo->createQueryBuilder('o')
            ->select('o.updatedAt, o.currency, o.amount, o.cart, o.deliveredAt')
            ->where('o.user = :user')->setParameter('user', $this->getUser())
            ->andWhere('o.status = :status')->setParameter('status', 'COMPLETED')
            ->andWhere('o.shipped != :shipped')->setParameter('shipped', true)
            ->getQuery()->getResult()
        ;
        
        $context = $this->orders($orders);

        return $this->render('order/to_be_shipped.html.twig', [
            'orders' => $context,
        ]);
    }

    /**
     * @Route("/unpaid", name="unpaid", methods={"GET"})
     */
    public function unpaid(): Response
    {
        // products where the order is cancled or not approved or there is an error
        $orders = $this->orderRepo->createQueryBuilder('o')
            ->select('o.updatedAt, o.currency, o.amount, o.cart, o.deliveredAt, o.approveLink')
            ->where('o.user = :user')->setParameter('user', $this->getUser())
            ->andWhere('o.status != :status')->setParameter('status', 'COMPLETED')
            ->getQuery()->getResult()
        ;

        $context = $this->orders($orders);

        return $this->render('order/unpaid.html.twig', [
            'orders' => $context,
        ]);
    }

    /**
     * @Route("/shipped", name="shipped", methods={"GET"})
     */
    public function shipped(): Response
    {
        // products who shipped (status updated by the admin)
        $orders = $this->orderRepo->createQueryBuilder('o')
            ->select('o.updatedAt, o.currency, o.amount, o.cart, o.deliveredAt')
            ->where('o.user = :user')->setParameter('user', $this->getUser())
            ->andWhere('o.shipped = :shipped')->setParameter('shipped', true)
            ->getQuery()->getResult()
        ;

        $context = $this->orders($orders);

        return $this->render('order/shipped.html.twig', [
            'orders' => $context,
        ]);
    }

    private function orders(array $orders): array
    {
        $context = [];

        foreach ($orders as $order) {
            $data = [];
            $data['amount'] = $order['amount'];
            $data['date'] = $order['updatedAt'];
            $data['currency'] = $order['currency'];
            $data['deliveredAt'] = $order['deliveredAt'];

            if (!empty($order['approveLink'])) {
                $data['link'] = $order['approveLink'];
            }

            $cart = $order['cart'] ?: [];

            $products = $this->productRepo->findProductsById(array_keys($cart));

            /** @var Product $product */
            foreach ($products as $product) {
                $data['products'][] = [
                    'name' => $product->getName(),
                    'id' => $product->getId(),
                    'quantity' => $cart[$product->getId()],
                    'price' => $product->getPrice(),
                    'image' => $product->getImage(),
                    'subtotal' => $product->getPrice() * $cart[$product->getId()],
                ];
            }
            $context[] = $data;
        }

        return $context;
    }
}
