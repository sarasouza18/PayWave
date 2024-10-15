<?php

namespace App\Tests\Service;

use App\Service\PaymentService;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PaymentStatusEntity;
use App\Enum\PaymentGateway;

class PaymentServiceFallbackTest extends TestCase
{
    private $paymentService;
    private $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->paymentService = new PaymentService(
            'stripe_secret_key',
            'paypal_client_id',
            'paypal_secret',
            $this->entityManager
        );
    }

    public function testFallbackToPayPal(): void
    {
        $paymentStatus = new PaymentStatusEntity();
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(PaymentStatusEntity::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->paymentService->processPayment(PaymentGateway::PAYPAL, 100);

        $this->assertNotNull($result);
        $this->assertIsString($result);
    }
}
