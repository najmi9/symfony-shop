<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $productRepo, SessionInterface $session, PaginatorInterface $paginator, Request $request, CategoryRepository $categoryRepo): Response
    {
        $products = $productRepo->findAll();
        $products = $paginator->paginate(
            $products, 
            $request->query->getInt('page', 1), /*page number*/
            6
        );
        $items = 0;
        foreach ($session->get('cart', []) as $key => $value) {
            $items += $value;
        }
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'items' => $items,
            'categories' => $categoryRepo->findAll()
        ]);
    }
}
