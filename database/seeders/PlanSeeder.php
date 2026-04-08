<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'price' => 0.00,
                'employee_limit' => 5,
            ],
            [
                'name' => 'Basic',
                'price' => 10.00,
                'employee_limit' => 50,
            ],
            [
                'name' => 'Standard',
                'price' => 30.00,
                'employee_limit' => 200,
            ],
            [
                'name' => 'Premium',
                'price' => 50.00,
                'employee_limit' => 999999, // Unlimited
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
