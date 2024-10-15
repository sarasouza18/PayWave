<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequest
{
    /**
     * @Assert\NotBlank(message="Amount should not be blank.")
     * @Assert\GreaterThan(value=0, message="Amount must be greater than zero.")
     * @Assert\Type(type="numeric", message="Amount must be a valid number.")
     */
    public int|float $amount;

    public function __construct(float $amount = 0)
    {
        $this->amount = $amount;
    }
}
