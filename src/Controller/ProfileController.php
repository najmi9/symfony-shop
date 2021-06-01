<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/profile", name="users")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/edit-shipping-data", name="edit", methods={"POST"})
     */
    public function edit(EntityManagerInterface $em, Request $request, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $user->setAddress($data['address'])
            ->setZip($data['zip'])
            ->setCity($data['city'])
            ->setName($data['name'])
            ->setMobile($data['mobile'])
        ;

        $errors = $validator->validate($user);

        if (\count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($user);

        $em->flush();

        return $this->json(['id' => $user->getId()]);
    }
}
