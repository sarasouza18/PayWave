<?php

namespace App\Enum;

class PaymentStatus
{
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const FAILED = 'failed';
    const CANCELED = 'canceled';

    public static function getAllStatuses(): array
    {
        return [
            self::PENDING,
            self::COMPLETED,
            self::FAILED,
            self::CANCELED,
        ];
    }
}