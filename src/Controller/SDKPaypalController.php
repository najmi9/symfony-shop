<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Service\Paypal\CreateOrderService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *  Create an new order
 * 
 * @IsGranted("ROLE_USER")
 */
class SDKPaypalController extends AbstractController
{
    /**
     * @Route("/pay")
     *
     * @return void
     */
    public function index(CreateOrderService $createOrder, EntityManagerInterface $em)
    {
        $env = new SandboxEnvironment($this->getParameter('paypal_id'), $this->getParameter('paypal_secret'));

        $client = new PayPalHttpClient($env);
        /** @var mixed */
        $response = $createOrder->createOrder($client);

        /** @var Order $order */
        $order = new Order();

        $order->setAmount($response->result->purchase_units[0]->amount->value)
            ->setApproveLink($response->result->links[1]->href)
            ->setCreatedAt(new \DateTime($response->result->create_time))
            ->setIdentifiant($response->result->id)
            ->setStatus($response->result->status)
            ->setPayee($response->result->purchase_units[0]->payee->email_address)
            ->setUser($this->getUser())
        ;
        $em->persist($order);
        $em->flush();
        //return $this->redirect($response->result->links[1]->href);
        return $this->json(['id' => $response->result->id]);
    }

    /**
     * @Route("/js")
     */
    public function pay()
    {
        return $this->render('paypal/pay.html.twig');
    }
}
