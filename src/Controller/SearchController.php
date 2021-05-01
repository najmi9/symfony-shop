<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(Request $request, ProductRepository $productRepo, SessionInterface $session, PaginatorInterface $paginator, CategoryRepository $categoryRepo): Response
    {
        $q = $request->query->get('q');
        $min = (float) $request->query->get('min', null);
        $max = (float) $request->query->get('max', null);
    	$categories = (array) $request->query->get('categories') ?? []; 

    	$products = $productRepo->findByName($q, $min, $max, $categories);

        $products = $paginator->paginate(
            $products, 
            $request->query->getInt('page', 1), /*page number*/
            6
        );
        $items = 0;
        foreach ($session->get('cart', []) as $key => $value) {
            $items += $value;
        }
        return $this->render('home/home.html.twig', [
            'products' => $products,
            'items' => $items,
            'categories' => $categoryRepo->findAll()
        ]);
    }
}
