<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use PayPalHttp\HttpException;
use App\Service\Paypal\CreateOrderService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use Symfony\Component\HttpFoundation\Request;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use App\Service\ProjectConstants;

/**
 * Pay Controller
 * 
 * @IsGranted("ROLE_USER")
 * @Route("/paypal", name="paypal_")
 */
class PaypalController extends AbstractController
{
    /**
     * @Route("/send-payment/{total}", name="send_payment", methods={"GET"})
     */
    public function pay(string $total): Response
    {

        if ($total<=0) {
            $this->addFlash("warning", "Total should not be negative or null.");
            return $this->redirectToRoute('cart');
        }

        return $this->render('paypal/pay.html.twig', compact('total'));
    }


    /**
     * @Route("/cancel", name="paypal_cancel",  methods={"POST"})
     */
    public function cancelPayment(Request $request)
    {
        dd($request);
    }

    /**
     * @Route("/error", name="paypal_error", methods={"POST"})
     */
    public function error(Request $request, EntityManagerInterface $em)
    {

       /*
        $payerId = $request->query->get('PayerID');
        $token = $request->query->get('token');

        $payment = new Payment();

        $payment->setUser($this->getUser())
            ->setToken($token)
            ->setPayeerId($payerId)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setStatus('NOT APPROVED');
        $em->persist($payment);
        $em->flush();

        return new Response('Thank you for your payment !');
        */
    }

    /**
     * capture the money
     *
     * @Route("/capture-payment", name="capture_payment", methods={"POST"})
     */
    public function captureOrder(Request $request, EntityManagerInterface $em, OrderRepository $orderRepo): JsonResponse
    {
        $env = new SandboxEnvironment($this->getParameter('paypal_id'), $this->getParameter('paypal_secret'));

        $client = new PayPalHttpClient($env);

        $result = json_decode($request->getContent(), true);

        // Here, OrdersCaptureRequest() creates a POST request to /v2/checkout/orders
        // $response->result->id gives the orderId of the order created above
        /** @var Order $order */
        $order = $orderRepo->findOneByIdentifiant($result['orderID']);
        $request = new OrdersCaptureRequest($result['orderID']);
        $request->prefer('return=representation');

        // Call API with your client and get a response for your call
        /** @var mixed */
        $response = $client->execute($request);

        // If call returns body in response, you can get the deserialized version from the result attribute of the response
        $order->setUpdatedAt(new \DateTime())
            ->setStatus('CAPTURED')
            ->setFacilitatorAccessToken($result['facilitatorAccessToken'])
            ->setPayeerId($result['payerID'])
            ->setPaymentId($result['paymentID'])
            ->setBillingToken($result['billingToken'])
        ;

        $em->persist($order);
        $em->flush();

        return $this->json([
            'message' => 'order completed successfly!',
            'id' => $order->getId() ,
            'amount' => $order->getAmount()
        ]);
    }

    /**
     * @Route("/create-order", name="create_order", methods={"POST", "GET"})
     */
    public function createOrder(SessionInterface $session, CreateOrderService $createOrder, EntityManagerInterface $em, ProductRepository $productRepo): JsonResponse
    {
        $env = new SandboxEnvironment($this->getParameter('paypal_id'), $this->getParameter('paypal_secret'));

        $client = new PayPalHttpClient($env);

        $cart = $session->get('cart', []);

        $products = $productRepo->findById(array_keys($cart));

        $subtotal = 0;

        $items = [];
        foreach ($products as $product) {
            $subtotal += round($product->getPrice()) * $cart[$product->getId()];
            $items[]= [
                'name' => $product->getName(),
                'description' => $product->getCategory()->getTitle(),
                'quantity' => (string) $cart[$product->getId()],
                'unit_amount' => [
                    'currency_code' => 'USD',
                    'value' =>  (string) round($product->getPrice()),
                ],
                'category' => 'PHYSICAL_GOODS',
            ];
        }

        $address = 'SET_PROVIDED_ADDRESS'; //$this->getUser()->getAddress() ?? 'address of paypal';

        $shippingPrice = ProjectConstants::SHIPPING_PRICE;
        $currency = ProjectConstants::CURRENCY;
        $handlingPrice = ProjectConstants::HANDLINH_PRICE;
        $total = $subtotal + $shippingPrice + $handlingPrice;
       
        $description = 'DESCRIPTION OF ORDER';

        /** @var array $body */
        $body = $createOrder->buildRequestBody(
            $items,
            (string) $subtotal,  
            (string) $total,
            (string) $shippingPrice,
            (string) $handlingPrice,
            $currency,
            $address,
            $description
        );

        /** @var mixed */
        $response = $createOrder->createOrder($client, $body);

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
        return $this->json(['id' => $response->result->id]);
    }
}
