<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customers/new", name="customer_new", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator,
     CustomerRepository $customerRepo): JsonResponse
    {
        try {
            /** @var Customer $customer */
            $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        } catch (NotEncodableValueException $th) {
            $this->json(['msg' => 'Syntax Error'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $oldCustomer = $customerRepo->findOneBy(['email' => $customer->getEmail(), 'mobile' => $customer->getMobile()]);

        if ($oldCustomer) {
            $oldCustomer->setCity($customer->getCity())
                ->setAddress($customer->getAddress())
                ->setZip($customer->getZip())
                ->setName($customer->getName())
            ;

            $em->persist($oldCustomer);
        } else {
            $em->persist($customer);
        }

        $em->flush();

        return $this->json([
            'id' => $customer->getId() ?: $oldCustomer->getId(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/customers/{id}/edit", name="customer_edit", methods={"POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Customer $customer): JsonResponse
    {
        try {
            /** @var Customer $cstmr */
            $cstmr = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        } catch (NotEncodableValueException $th) {
            $this->json(['msg' => 'Syntax Error'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $customer->setName($cstmr->getName() ?: $customer->getName())
            ->setName($cstmr->getName() ?: $customer->getName())
            ->setMobile($cstmr->getMobile() ?: $customer->getMobile())
            ->setCity($cstmr->getCity() ?: $customer->getCity())
            ->setAddress($cstmr->getAddress() ?: $customer->getAddress())
            ->setZip($cstmr->getZip() ?: $customer->getZip())
            ->setEmail($cstmr->getEmail() ?: $customer->getEmail())
        ;

        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($customer);

        $em->flush();

        return $this->json([
            'id' => $customer->getId(),
        ], Response::HTTP_OK);
    }
}
