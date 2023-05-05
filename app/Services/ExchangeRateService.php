<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function convertToEur(float $amount, float $exchangeRate)
    {
        return $amount * (1 / $exchangeRate);
    }

    public function convertFromEur(float $amount, float $exchangeRate)
    {
        return $amount * $exchangeRate;
    }

    public function getExchangeRate(string $currency)
    {
        $result = Http::get(config('exchange-rates.url'))->json();

        return $result['rates'][$currency];
    }
}
