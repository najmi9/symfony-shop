<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ProductType;
use App\Entity\Product;

class ProductController extends AbstractController
{
    
    /**
     * @Route("/product/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('upload_images', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/upload-images/{id}", name="upload_images", methods={"GET"})
     */
    public function uploadImages(string $id)
    {
       return $this->render('admin/product/upload_images.html.twig', [
            'id' => $id,
        ]); 
    }

    
     /**
     * @Route("/upload-image/{id}", name="upload_image", methods={"POST"})
     */
    public function uploadImage(string $id=null, Request $request, FileUploader $fileUploader,
     EntityManagerInterface $em, ProductRepository $productRepo)
    {
        /* $file = $request->files->get('file');
        
        $product = $productRepo->find($id);

        $images = $product->getImages() ?: [];

        $fileName = $fileUploader->uploadProductPicture($file, $product->getCreatedAt()->getTimestamp());

        if ($fileName) {
            $images[] = $fileName;
            $product->setImages($images);
            $em->persist($product);
            $em->flush();
        }

        return $this->json([
            'message' => 'Succcess',
        ], 201); */
    }

    /**
     * @Route("/delete-image/{index}/product/{id}", name="delete_image", methods={"GET"})
     */
    public function deleteImages(int $id, int $index, FileUploader $fileUploader, 
    EntityManagerInterface $em, ProductRepository $productRepo)
    {
        /* $product = $productRepo->findOneById($id);
        $images = $product->getImages();
        $toDelete = array_splice($images, $index, 1);
        $product->setImages($images);
        $fileUploader->deleteProductImage($product->getCreatedAt()->getTimestamp(), $toDelete);
        $em->persist($product);
        $em->flush();
        $this->addFlash('success', 'Image deleted Successfly.');

        return $this->redirectToRoute('website_edit'); */
    }
    
}
