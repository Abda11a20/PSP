<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::create([
            'name' => 'Test Company Inc.',
            'email' => 'admin@testcompanyinc.com',
            'password' => Hash::make('password123'),
            'plan_id' => 2, // Basic plan
        ]);

        // Create a test campaign
        $campaign = Campaign::create([
            'company_id' => $company->id,
            'type' => 'phishing',
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

        // Create test targets
        $targets = [
            [
                'campaign_id' => $campaign->id,
                'name' => 'John Doe',
                'email' => 'john.doe@testcompanyinc.com',
            ],
            [
                'campaign_id' => $campaign->id,
                'name' => 'Jane Smith',
                'email' => 'jane.smith@testcompanyinc.com',
            ],
            [
                'campaign_id' => $campaign->id,
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@testcompanyinc.com',
            ],
        ];

        foreach ($targets as $targetData) {
            CampaignTarget::create($targetData);
        }

        $this->command->info('Test company, campaign, and targets created successfully!');
    }
}
