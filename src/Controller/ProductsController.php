<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ProductsController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="product_show")
     */
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }
}
