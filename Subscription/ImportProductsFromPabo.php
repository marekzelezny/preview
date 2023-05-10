<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionType;
use App\Enums\TrioboType;
use App\Extensions\PeriodikAbo\PeriodikAbo;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ImportProductsFromPabo extends Command
{
    protected $signature = 'pabo:import-products';

    protected $description = 'Import products from PABO';

    public function handle(): void
    {
        $paboProducts = PeriodikAbo::prices()->object();

        foreach ($paboProducts as $paboProduct) {
            $model = Subscription::updateOrCreate(
                [
                    'pabo_id' => $paboProduct->id,
                ],
                [
                    'title' => $paboProduct->title,
                    'type' => $this->getSubscriptionType($paboProduct->title),
                    'price' => $paboProduct->price,
                    'period' => $paboProduct->period,
                    'triobo_id' => $this->getTrioboType($paboProduct->title),
                ]
            );

            $this->info("Product {$model->title} was imported/updated.");
        }
    }

    public function getSubscriptionType($title): SubscriptionType
    {
        return match (true) {
            str_contains($title, 'Kombinované') => SubscriptionType::COMBINED,
            str_contains($title, 'Digitální') => SubscriptionType::DIGITAL,
            default => SubscriptionType::PRINT,
        };
    }

    public function getTrioboType($title): ?TrioboType
    {
        if (! str_contains($title, 'Digitální')) {
            return null;
        }

        return match (true) {
            str_contains($title, 'roční') => TrioboType::YEAR,
            str_contains($title, 'půlroční') => TrioboType::HALFYEAR,
            str_contains($title, 'čtvrtletní') => TrioboType::QUARTER,
            str_contains($title, 'měsíční') => TrioboType::MONTH,
            default => null,
        };
    }
}
