<?php

namespace App\Services\Operations\Withdraws;

use App\Enums\UserType;

class WithdrawFactory
{
    public static function getUserType(string $key): WithdrawInterface
    {
        return match ($key) {
            UserType::PRIVATE->value => new PrivateUserWithdraw(),
            UserType::BUSINESS->value => new BusinessUserWithdraw(),
            default => throw new \Exception('Unsupported user type'),
        };
    }
}
