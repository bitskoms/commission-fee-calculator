<?php

namespace App\Services\Operations;

use App\Enums\OperationType;

class OperationFactory
{
    /**
     * @throws \Exception
     */
    public static function getOperationType(string $key): OperationInterface
    {
        return match ($key) {
            OperationType::DEPOSIT->value => new Deposit(),
            OperationType::WITHDRAW->value => new Withdraw(),
            default => throw new \Exception('Unsupported operation type'),
        };
    }
}
