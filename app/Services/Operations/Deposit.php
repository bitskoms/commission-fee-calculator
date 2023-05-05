<?php

namespace App\Services\Operations;

class Deposit implements OperationInterface
{
    private float $amount;

    private float $operationFeePercentage = 0.03;

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
