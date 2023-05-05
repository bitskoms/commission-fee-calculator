<?php

namespace App\Services\Operations\Withdraws;

use App\Services\Operations\OperationInterface;

class BusinessUserWithdraw implements OperationInterface
{
    private float $amount;

    private float $operationFeePercentage = 0.5;

    public function setChargeAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function calculateFee(): float
    {
        $operationFee = $this->amount * $this->operationFeePercentage / 100;

        return ceil($operationFee * 100) / 100;
    }
}
