<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testProcessPayment()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/payments',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'amount' => 150.50,
                'currency' => 'USD',
                'gateway' => 'stripe'
            ])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('payment_id', $responseContent);
        $this->assertNotNull($responseContent['payment_id']);
    }

    public function testGetPaymentStatus()
    {
        $client = static::createClient();

        $client->request('GET', '/api/payments/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('status', $responseContent);
        $this->assertEquals('completed', $responseContent['status']);
    }
}
