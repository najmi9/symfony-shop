<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

/**
 * @Route("/products", name="products_")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/{id}/details", name="show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }
}
