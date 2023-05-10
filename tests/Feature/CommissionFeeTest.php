<?php

namespace Tests\Feature;

use App\Services\CommissionFeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommissionFeeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_commission(): void
    {
        $filePath = storage_path("input.csv");

        $commissionFeeCalculator = new CommissionFeeCalculator($filePath);

        $result = $commissionFeeCalculator->calculate();

        $this->assertEquals([
            0.6,
            3,
            0,
            0.06,
            1.5,
            0,
            0.69,
            0.3,
            0.3,
            3,
            0,
            0,
            8607.4,
        ], $result);
    }
}
