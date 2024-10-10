<?php

namespace App\Entity;

use App\Enum\PaymentGateway;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PaymentGatewayEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $gateway;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGateway(): string
    {
        return $this->gateway;
    }

    public function setGateway(string $gateway): self
    {
        if (!in_array($gateway, [PaymentGateway::STRIPE, PaymentGateway::PAYPAL])) {
            throw new \InvalidArgumentException("Invalid payment gateway");
        }
        $this->gateway = $gateway;
        return $this;
    }
}
