<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ProductType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/admin/products", name="admin_products_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ProductRepository $productRepo): Response
    {
        return $this->render('admin/product/index.html.twig', ['products' => $productRepo->findAll()]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_products_upload_images', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/upload-images/{id}", name="upload_images", methods={"GET"})
     */
    public function uploadImages(Product $product):Response
    {
       return $this->render('admin/product/upload_images.html.twig', [
            'product' => $product,
        ]); 
    }

     /**
     * @Route("/upload-image/{id}", name="upload_image", methods={"POST"})
     */
    public function uploadImage(Product $product, Request $request, FileUploader $fileUploader, EntityManagerInterface $em): JsonResponse
    {
        $file = $request->files->get('file');

        $image = $fileUploader->uploadProductPicture($file, $product->getId());

        $product->addImage($image);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Succcess',
        ], 201);
    }

    /**
     * @Route("/upload-cover-image/{id}", name="upload_cover_image", methods={"POST"})
     */
    public function uploadCoverImage(Product $product, Request $request, FileUploader $fileUploader, EntityManagerInterface $em): JsonResponse
    {
        $file = $request->files->get('file');

        if ($product->getImage()) {
            $fileUploader->deleteProductImage($product->getId(), $product->getImage());
        }

        $image = $fileUploader->uploadProductPicture($file, $product->getId());

        $product->setImage($image);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Succcess',
        ], 201);
    }


    /**
     * @Route("/{id}/delete-image/{image}", name="delete_image", methods={"GET"})
     */
    public function deleteImage(Product $product, string $image, FileUploader $fileUploader, EntityManagerInterface $em): RedirectResponse
    {
        $product->removeImage($image);

        $fileUploader->deleteProductImage($product->getId(), $image);
        $em->persist($product);
        $em->flush();
        $this->addFlash('success', 'Product media deleted.');

        return $this->redirectToRoute('admin_products_upload_images', ['id' => $product->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Product $product, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_products_upload_images', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET"})
     */
    public function delete(Product $product, EntityManagerInterface $em, FileUploader $fileUploader): RedirectResponse
    {
        $fileUploader->removeProductDir($product->getId());
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Product deleted.');

        return $this->redirectToRoute('admin_products_index');
    }
}
