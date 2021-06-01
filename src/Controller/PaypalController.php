<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\MailService;
use App\Service\Paypal\CreateOrderService;
use App\Service\Paypal\PaymentFailedException;
use App\Service\ProjectConstants;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/paypal", name="paypal_")
 */
class PaypalController extends AbstractController
{
    /**
     * @Route("/send-payment/{total}", name="send_payment", methods={"GET", "POST"})
     */
    public function pay(float $total): Response
    {
        $shippingPrice = ProjectConstants::SHIPPING_PRICE;
        $handlingPrice = ProjectConstants::HANDLINH_PRICE;
        $total = $total + $shippingPrice + $handlingPrice;

        return $this->render('paypal/pay.html.twig', [
            'total' => $total,
            'PAYPAL_ID' => $this->getParameter('paypal_id'),
            'customer' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/create-order/{id}", name="create_order", methods={"POST"}, defaults={"id": null})
     */
    public function createOrder(Customer $customer = null, SessionInterface $session, CreateOrderService $createOrder, EntityManagerInterface $em, CartService $cartService): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user && !$customer) {
            return $this->json(['msg' => 'Customer Required.'], Response::HTTP_BAD_REQUEST);
        }

        $client = $this->getClient();
        $cart = $session->get('cart', []);
        $payment = $cartService->generatePayment($cart);

        try {
            /** @var \stdClass $response */
            $response = $createOrder->createOrder($client, $payment);
            $order = new Order();

            $order->setAmount($response->result->purchase_units[0]->amount->value)
                ->setApproveLink($response->result->links[1]->href)
                ->setCreatedAt(new \DateTime($response->result->create_time))
                ->setIdentifiant($response->result->id)
                ->setStatus($response->result->status)
                ->setPayee($response->result->purchase_units[0]->payee->email_address)
                ->setUser($user)
                ->setCart($cart)
                ->setCurrency($response->result->purchase_units[0]->amount->currency_code);
            ;

            if ($customer) {
                $order->setCustomer($customer);
            }

            $session->clear();

            if ($user) {
                $user->setCart([]);
                $em->persist($user);
            }

            $em->persist($order);
            $em->flush();

            return $this->json(['id' => $response->result->id]);
        } catch (PaymentFailedException $e) {
            return  $this->json(['message' => 'Error of payment '.$e->getMessage()],  Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/capture-payment", name="capture_payment", methods={"POST"})
     */
    public function captureOrder(Request $request, EntityManagerInterface $em, OrderRepository $orderRepo): JsonResponse
    {
        $client = $this->getClient();

        $result = json_decode($request->getContent(), true);

        // Here, OrdersCaptureRequest() creates a POST request to /v2/checkout/orders
        // $response->result->id gives the orderId of the order created above
        /** @var Order $order */
        $order = $orderRepo->findOneByIdentifiant($result['orderID']);
    
        $request = new OrdersCaptureRequest($order->getIdentifiant());
        $request->prefer('return=representation');

        // Call API with your client and get a response for your call
        /** @var \stdClass $capture */
        $capture = $client->execute($request);

        if ('COMPLETED' !== $capture->result->status) {
            throw new PaymentFailedException('Impossible to capturer this paiement');
        }

        $payerId = $capture->result->payer->payer_id;

        $capture = $capture->result->purchase_units[0]->payments->captures[0];

        // If call returns body in response, you can get the deserialized version from the result attribute of the response
        $order->setUpdatedAt(new \DateTime())
            ->setStatus('COMPLETED')
            ->setFacilitatorAccessToken($result['facilitatorAccessToken'])
            ->setPayeerId($payerId)
            ->setBillingToken($result['billingToken'])
            ->setCaptureid($capture->id)
            ->setFee((float) $capture->seller_receivable_breakdown->paypal_fee->value)
        ;

        $em->persist($order);
        $em->flush();

        return $this->json([
            'message' => 'order completed successfly!',
            'id' => $order->getId(),
            'amount' => $order->getAmount()
        ]);
    }

    /**
     * @Route("/paypal-return", name="return_url") 
     */
    public function returnAfterPayment(Request $request, OrderRepository $orderRepo, EntityManagerInterface $em): Response
    {
        // update the order to be be completed.
        $token = $request->query->get('token');

        if ($this->getParameter('env') == 'dev' || $this->getParameter('env') == 'test') {
            $url = "https://www.sandbox.paypal.com/checkoutnow?token={$token}";
        } else {
            $url = "https://www.paypal.com/checkoutnow?token={$token}";
        }
 
        $order = $orderRepo->findOneBy(['approveLink' => $url]);
 
        $order->setStatus('COMPLETED')
            ->setUpdatedAt(new \DateTime())
        ;
 
        $em->persist($order);
        $em->flush();
        $this->addFlash('success', 'Thank you for your payment');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/paypal-cancel", name="cancel_url") 
     */
    public function canceledPayment(Request $request, OrderRepository $orderRepo, EntityManagerInterface $em): Response
    {
        // update the order to be be completed.
        $token = $request->query->get('token');

        if ($this->getParameter('env') == 'dev' || $this->getParameter('env') == 'test') {
            $url = "https://www.sandbox.paypal.com/checkoutnow?token={$token}";
        }else {
            $url = "https://www.paypal.com/checkoutnow?token={$token}";
        }

        $order = $orderRepo->findOneBy(['approveLink' => $url]);

        $order->setStatus('CANCLED')
            ->setUpdatedAt(new \DateTime())
        ;

        $em->persist($order);
        $em->flush();

        $this->addFlash('danger', 'Payment Canceled');

        return $this->redirectToRoute('home');
    }

    private function getClient(): PayPalHttpClient
    {
        if ($this->getParameter('env') == 'dev' || $this->getParameter('env') == 'test') {
            $env = new SandboxEnvironment($this->getParameter('paypal_id'), $this->getParameter('paypal_secret'));
        } else {
             $env = new ProductionEnvironment($this->getParameter('paypal_id'), $this->getParameter('paypal_secret'));
        }

        return new PayPalHttpClient($env);
    }

    /**
     * @Route("/{identifiant}/order-cancled", name="order_cancled", methods={"POST"})
     */
    public function orderCancled(Order $order, EntityManagerInterface $em): JsonResponse
    {
        $order->setStatus('CANCLED')
            ->setUpdatedAt(new \DateTime())
        ;

        $em->persist($order);
        $em->flush();

        return $this->json('Success');
    }

     /**
     * @Route("/order-error", name="order_cancled", methods={"POST"})
     */
    public function orderError(Request $request,  MailService $mailService): JsonResponse
    {
        /* $mailService->sendEmail($this->getParameter('sender_email'), 'Error In Paypal Payment', 'emails/paypal_error.html.twig', [
            'msg' => $request->getContent(),
        ]); */

        return $this->json('Success');
    }
}
