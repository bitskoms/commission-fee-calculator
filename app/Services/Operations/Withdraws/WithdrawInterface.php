<?php

namespace App\Services\Operations\Withdraws;

interface WithdrawInterface
{
    public function calculateFee(): float;
}
