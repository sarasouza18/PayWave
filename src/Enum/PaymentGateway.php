<?php

namespace App\Enum;

class PaymentGateway
{
    const STRIPE = 'stripe';
    const PAYPAL = 'paypal';

    public static function getAllGateways(): array
    {
        return [
            self::STRIPE,
            self::PAYPAL,
        ];
    }
}