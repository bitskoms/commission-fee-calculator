<?php

namespace App\Console\Commands;

use App\Enums\Currency;
use App\Enums\OperationType;
use App\Enums\UserType;
use App\Services\ExchangeRateService;
use App\Services\Operations\OperationFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateFee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:fee {filePath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates operations fee';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path($this->argument('filePath'));

        if (!file_exists($filePath)) {
            $this->error('File not found');

            return;
        }

        $file = fopen($filePath, 'r');

        $userWithdraws = [];
        $exchangeRates = [];

        while ($row = fgetcsv($file)) {
            $operationDate = Carbon::parse($row[0]);
            $userId = $row[1];
            $userType = $row[2];
            $operationType = $row[3];
            $operationAmount = $row[4];
            $currency = $row[5];

            $chargeAmount = $operationAmount;

            if ($operationType == OperationType::WITHDRAW->value && $userType == UserType::PRIVATE->value) {
                if (!isset($userWithdraws[$userId])) {
                    $userWithdraws[$userId] = [];
                }

                $userWithdraw = $userWithdraws[$userId];
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
                        if (!isset($exchangeRates[$currency])) {
                            $exchangeRates[$currency] = $exchangeRateService->getExchangeRate($currency);
                        }

                        $amountInEur = $exchangeRateService->convertToEur(
                            $operationAmount,
                            $exchangeRates[$currency]
                        );
                    }

                    $weekOperation['totalAmount'] += $amountInEur;

                    if ($weekOperation['totalAmount'] > 1000) {
                        $chargeAmount = ($weekOperation['totalAmount'] - 1000);

                        if (!$isEuro) {
                            $chargeAmount = $exchangeRateService->convertFromEur(
                                $chargeAmount,
                                $exchangeRates[$currency]
                            );
                        }
                    } else {
                        $chargeAmount = 0;
                    }
                }

                $weekOperation['operationCount']++;

                $userWithdraws[$userId]['operationDates'][$startOfWeek] = $weekOperation;
            }

            $fee = OperationFactory::getOperationType($row[3], $row[2])
                ->setChargeAmount($chargeAmount)
                ->calculateFee();

            $this->line($fee);
        }

        fclose($file);
    }
}
