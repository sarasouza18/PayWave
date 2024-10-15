<?php

namespace App\Tests\Entity;

use App\Entity\PaymentStatusEntity;
use PHPUnit\Framework\TestCase;

class PaymentStatusEntityTest extends TestCase
{
    public function testStatusCanBeSetAndGet()
    {
        $paymentStatus = new PaymentStatusEntity();
        $paymentStatus->setStatus('pending');

        $this->assertEquals('pending', $paymentStatus->getStatus());
    }

    public function testPaymentId()
    {
        $paymentStatus = new PaymentStatusEntity();
        $paymentStatus->setId(1);

        $this->assertEquals(1, $paymentStatus->getId());
    }
}
