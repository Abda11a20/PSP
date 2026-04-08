<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;

class FixPlanPrices extends Command
{
    protected $signature = 'plans:fix-prices';
    protected $description = 'Fix invalid price values in plans table';

    public function handle()
    {
        $this->info('Checking and fixing plan prices...');
        
        $plans = Plan::all();
        $fixed = 0;
        
        foreach ($plans as $plan) {
            $rawPrice = $plan->getRawOriginal('price');
            
            if ($rawPrice === null || $rawPrice === '' || !is_numeric($rawPrice)) {
                $this->warn("Plan ID {$plan->id} ({$plan->name}) has invalid price: " . var_export($rawPrice, true));
                $plan->update(['price' => 0.00]);
                $fixed++;
                $this->info("  → Fixed to 0.00");
            }
        }
        
        if ($fixed > 0) {
            $this->info("Fixed {$fixed} plan(s).");
        } else {
            $this->info("All plan prices are valid.");
        }
        
        return 0;
    }
}
