<?php

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentFeatureTest extends WebTestCase
{
    public function testProcessPayment()
    {
        $client = static::createClient();

        $client->request('POST', '/api/payments', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'amount' => 100.00,
            'currency' => 'USD',
            'gateway' => 'stripe'
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('payment_id', $responseContent);
        $this->assertNotNull($responseContent['payment_id']);
    }

    public function testPaymentFallback()
    {
        $client = static::createClient();

        $client->request('POST', '/api/payments', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'amount' => 100.00,
            'currency' => 'USD',
            'gateway' => 'stripe'
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('paypal', $responseContent['used_gateway']);
    }
}
