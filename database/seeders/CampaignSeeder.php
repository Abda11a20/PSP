<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Seeding campaigns...');

        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createCampaignsForCompany($company);
        }

        $this->command->info('✅ Campaigns seeded successfully');
    }

    /**
     * Create campaigns for a specific company
     */
    private function createCampaignsForCompany(Company $company): void
    {
        $campaignCount = $this->getCampaignCountForPlan($company->plan_id);
        $campaignTypes = ['phishing', 'awareness', 'training'];

        for ($i = 1; $i <= $campaignCount; $i++) {
            $type = $campaignTypes[array_rand($campaignTypes)];
            $campaignData = $this->generateCampaignData($company, $type, $i);
            
            Campaign::create($campaignData);
        }
    }

    /**
     * Get campaign count based on company plan
     */
    private function getCampaignCountForPlan(int $planId): int
    {
        return match ($planId) {
            1 => 3, // Free plan - 3 campaigns
            2 => 8, // Basic plan - 8 campaigns
            3 => 15, // Standard plan - 15 campaigns
            4 => 25, // Premium plan - 25 campaigns
            5 => 50, // Enterprise plan - 50 campaigns
            default => 3,
        };
    }

    /**
     * Generate campaign data
     */
    private function generateCampaignData(Company $company, string $type, int $index): array
    {
        $startDate = $this->generateStartDate($index);
        $endDate = $startDate->copy()->addDays(rand(7, 30));
        $status = $this->determineStatus($startDate, $endDate, $index);

        return [
            'company_id' => $company->id,
            'type' => $type,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_at' => $startDate->subDays(rand(1, 5)),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate start date for campaign
     */
    private function generateStartDate(int $index): Carbon
    {
        $daysAgo = rand(1, 90);
        return now()->subDays($daysAgo);
    }

    /**
     * Determine campaign status based on dates
     */
    private function determineStatus(Carbon $startDate, Carbon $endDate, int $index): string
    {
        if ($startDate->isFuture()) {
            return 'draft';
        }

        if ($endDate->isPast()) {
            return 'completed';
        }

        // Mix of active and paused campaigns
        return $index % 4 === 0 ? 'paused' : 'active';
    }
}

