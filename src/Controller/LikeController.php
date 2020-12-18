<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LikeRepository;
use App\Repository\ProductRepository;
use App\Entity\Like;

class LikeController extends AbstractController
{
    /**
     * @Route("/like", name="like", methods={"POST"})
     */
    public function index(Request $request, EntityManagerInterface $em, LikeRepository $likeRepo, ProductRepository $productRepo): Response
    {
    	$id = json_decode($request->getContent(), true);
    	$user = $this->getUser();
    	$product = $productRepo->find($id);
    	$isLiked = $user->isLikedByUser($id);

    	if ($isLiked) {
    		$like = $likeRepo->findOneByUserAndProduct($user->getId(), $id);
    		$em->remove($like);
    	}else{
    		$like = new Like();
    		$like->setUser($user)
    			->setProduct($product)
    		;
    		$em->persist($like);
    	}
    	$em->flush();

        return $this->json([
        	'likes' => count($product->getLikes()),
        ], 200);
    }
}
