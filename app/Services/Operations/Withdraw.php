<?php

namespace App\Services\Operations;

class Withdraw implements OperationInterface
{
    private float $amount;
    private string $userType;

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setUserType()
    {

    }

    public function calculateFee(): float
    {
        // TODO: Implement calculateFee() method.
    }
}
