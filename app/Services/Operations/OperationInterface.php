<?php

namespace App\Services\Operations;

interface OperationInterface
{
    public function setAmount(float $amount): self;

    public function calculateFee(): float;
}
