<?php

namespace App\Console\Commands;

use App\Services\CommissionFeeCalculator;
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

        $commissionFeeCalculator = new CommissionFeeCalculator($filePath);

        $result = $commissionFeeCalculator->calculate();

        foreach ($result as $item) {
            $this->line($item);
        }
    }
}
