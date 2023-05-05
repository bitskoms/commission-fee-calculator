<?php

namespace App\Services\Operations;

interface OperationInterface
{
    public function setChargeAmount(float $amount): self;

    public function calculateFee(): float;
}
