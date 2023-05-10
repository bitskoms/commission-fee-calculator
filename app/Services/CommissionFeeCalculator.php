<?php

namespace App\Services;

use App\Enums\Currency;
use App\Enums\OperationType;
use App\Enums\UserType;
use App\Services\Operations\OperationFactory;
use Carbon\Carbon;

class CommissionFeeCalculator
{
    private array $userWithdraws = [];
    private array $exchangeRates = [];

    public function __construct(private string $filePath)
    {
    }

    public function calculate(): array
    {
        $file = fopen($this->filePath, 'r');

        $result = [];

        while ($row = fgetcsv($file)) {
            $operationDate = Carbon::parse($row[0]);
            $userId = $row[1];
            $userType = $row[2];
            $operationType = $row[3];
            $operationAmount = $row[4];
            $currency = $row[5];

            $chargeAmount = $operationAmount;

            if ($operationType == OperationType::WITHDRAW->value && $userType == UserType::PRIVATE->value) {
                if (!isset($this->userWithdraws[$userId])) {
                    $this->userWithdraws[$userId] = [];
                }

                $userWithdraw = $this->userWithdraws[$userId];
                $startOfWeek = $operationDate->startOfWeek()->toDateString();

                if (!isset($userWithdraw['operationDates'][$startOfWeek])) {
                    $userWithdraw['operationDates'][$startOfWeek] = [
                        'operationCount' => 0,
                        'totalAmount' => 0,
                    ];
                }

                $weekOperation = $userWithdraw['operationDates'][$startOfWeek];

                if ($weekOperation['operationCount'] < 3 && $weekOperation['totalAmount'] <= 1000) {
                    $isEuro = $currency === Currency::EUR->value;
                    $exchangeRateService = new ExchangeRateService();

                    $amountInEur = $operationAmount;

                    if (!$isEuro) {
                        if (!isset($this->exchangeRates[$currency])) {
                            $this->exchangeRates[$currency] = $exchangeRateService->getExchangeRate($currency);
                        }

                        $amountInEur = $exchangeRateService->convertToEur(
                            $operationAmount,
                            $this->exchangeRates[$currency]
                        );
                    }

                    $weekOperation['totalAmount'] += $amountInEur;

                    if ($weekOperation['totalAmount'] > 1000) {
                        $chargeAmount = ($weekOperation['totalAmount'] - 1000);

                        if (!$isEuro) {
                            $chargeAmount = $exchangeRateService->convertFromEur(
                                $chargeAmount,
                                $this->exchangeRates[$currency]
                            );
                        }
                    } else {
                        $chargeAmount = 0;
                    }
                }

                $weekOperation['operationCount']++;

                $this->userWithdraws[$userId]['operationDates'][$startOfWeek] = $weekOperation;
            }

            $fee = OperationFactory::getOperationType($row[3], $row[2])
                ->setChargeAmount($chargeAmount)
                ->calculateFee();

            $result[] = $fee;
        }

        fclose($file);

        return $result;
    }
}
