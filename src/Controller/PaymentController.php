<?php

namespace App\Controller;

use App\Dto\PaymentRequest;
use App\Enum\PaymentGateway;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @Route("/pay/stripe", name="pay_stripe", methods={"POST"})
     */
    public function payWithStripe(PaymentRequest $paymentRequest): Response
    {
        try {
            $paymentIntent = $this->paymentService->processPayment(PaymentGateway::STRIPE, $paymentRequest->amount);
            return $this->json([
                'status' => 'success',
                'paymentIntent' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/pay/paypal", name="pay_paypal", methods={"POST"})
     */
    public function payWithPayPal(PaymentRequest $paymentRequest): Response
    {
        try {
            $orderId = $this->paymentService->processPayment(PaymentGateway::PAYPAL, $paymentRequest->amount);
            return $this->json([
                'status' => 'success',
                'orderId' => $orderId,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
