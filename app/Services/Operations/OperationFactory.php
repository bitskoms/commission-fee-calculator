<?php

namespace App\Services\Operations;

use App\Enums\OperationType;
use App\Enums\UserType;
use App\Services\Operations\Withdraws\BusinessUserWithdraw;
use App\Services\Operations\Withdraws\PrivateUserWithdraw;

class OperationFactory
{
    /**
     * @throws \Exception
     */
    public static function getOperationType(string $operationType, string $userType): OperationInterface
    {
        return match ($operationType) {
            OperationType::DEPOSIT->value => new Deposit(),
            OperationType::WITHDRAW->value => match ($userType) {
                UserType::PRIVATE->value => new PrivateUserWithdraw(),
                UserType::BUSINESS->value => new BusinessUserWithdraw(),
                default => throw new \Exception('Unsupported user type'),
            },
            default => throw new \Exception('Unsupported operation type'),
        };
    }
}
