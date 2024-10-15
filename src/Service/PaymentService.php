<?php

namespace App\Service;

use App\Entity\PaymentStatusEntity;
use App\Enum\PaymentGateway;
use App\Enum\PaymentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttp\HttpException;

class PaymentService
{
    private string $stripeSecret;
    private PayPalHttpClient $paypalClient;
    private EntityManagerInterface $entityManager;

    public function __construct(string $stripeSecret, string $paypalClientId, string $paypalSecret, EntityManagerInterface $entityManager)
    {
        $this->stripeSecret = $stripeSecret;

        $environment = new SandboxEnvironment($paypalClientId, $paypalSecret);
        $this->paypalClient = new PayPalHttpClient($environment);
        $this->entityManager = $entityManager;
    }

    /**
     * Main function to process payment.
     * Accepts the gateway name (stripe or paypal) and directs to the corresponding method.
     * @throws Exception
     */
    public function processPayment(string $gateway, float $amount, string $currency = 'usd'): PaymentIntent
    {
        $paymentStatus = new PaymentStatusEntity();
        $paymentStatus->setStatus(PaymentStatus::PENDING); // Set initial status

        $this->entityManager->persist($paymentStatus);
        $this->entityManager->flush();

        if ($gateway === PaymentGateway::STRIPE) {
            return $this->createStripePayment($amount, $currency, $paymentStatus);
        } elseif ($gateway === PaymentGateway::PAYPAL) {
            return $this->createPayPalPayment($amount, $paymentStatus);
        } else {
            throw new Exception("Invalid payment gateway");
        }
    }

    /**
     * Process payment via Stripe.
     * @throws Exception
     */
    private function createStripePayment(float $amount, string $currency, PaymentStatusEntity $paymentStatus): PaymentIntent
    {
        Stripe::setApiKey($this->stripeSecret);

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => $currency,
                'payment_method_types' => ['card'],
            ]);
            $paymentStatus->setStatus(PaymentStatus::COMPLETED);
            return $paymentIntent;
        } catch (Exception $e) {
            $paymentStatus->setStatus(PaymentStatus::FAILED);
            throw new Exception("Error processing payment via Stripe: " . $e->getMessage());
        } finally {
            $this->entityManager->persist($paymentStatus);
            $this->entityManager->flush();
        }
    }

    /**
     * Process payment via PayPal.
     */
    private function createPayPalPayment(float $amount, PaymentStatusEntity $paymentStatus)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $amount,
                    ]
                ]
            ]
        ];

        try {
            $response = $this->paypalClient->execute($request);
            $paymentStatus->setStatus(PaymentStatus::COMPLETED);
            return $response->result->id;
        } catch (HttpException $e) {
            $paymentStatus->setStatus(PaymentStatus::FAILED);
            throw new Exception("Error processing payment via PayPal: " . $e->getMessage());
        } finally {
            $this->entityManager->persist($paymentStatus);
            $this->entityManager->flush();
        }
    }

    /**
     * Updates the payment status in the database.
     *
     * @param int $paymentId
     * @param string $status
     * @throws Exception if the status is invalid
     */
    public function updatePaymentStatus(int $paymentId, string $status): void
    {
        if (!in_array($status, PaymentStatus::getAllStatuses())) {
            throw new Exception("Invalid payment status");
        }

        $paymentStatus = $this->entityManager->getRepository(PaymentStatusEntity::class)->find($paymentId);

        if (!$paymentStatus) {
            throw new Exception("Payment status not found");
        }

        $paymentStatus->setStatus($status);

        $this->entityManager->persist($paymentStatus);
        $this->entityManager->flush();
    }
}