<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\Interaction;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Seeding interactions...');

        $campaigns = Campaign::with('targets')->get();

        foreach ($campaigns as $campaign) {
            $this->createInteractionsForCampaign($campaign);
        }

        $this->command->info('✅ Interactions seeded successfully');
    }

    /**
     * Create interactions for a specific campaign
     */
    private function createInteractionsForCampaign(Campaign $campaign): void
    {
        foreach ($campaign->targets as $target) {
            $this->createInteractionsForTarget($campaign, $target);
        }
    }

    /**
     * Create interactions for a specific target
     */
    private function createInteractionsForTarget(Campaign $campaign, CampaignTarget $target): void
    {
        $interactionTypes = ['sent', 'opened', 'clicked', 'submitted'];
        $interactionCount = $this->getInteractionCount($campaign, $target);
        $selectedInteractions = array_slice($interactionTypes, 0, $interactionCount);

        $baseTime = $campaign->start_date;
        
        foreach ($selectedInteractions as $index => $actionType) {
            $timestamp = $this->generateTimestamp($baseTime, $index, $actionType);
            
            Interaction::create([
                'campaign_id' => $campaign->id,
                'email' => $target->email,
                'action_type' => $actionType,
                'timestamp' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Get interaction count based on campaign type and target
     */
    private function getInteractionCount(Campaign $campaign, CampaignTarget $target): int
    {
        // Base interaction rates by campaign type
        $baseRates = match ($campaign->type) {
            'phishing' => [
                'sent' => 100,    // 100% of targets receive emails
                'opened' => 75,   // 75% open rate
                'clicked' => 25,  // 25% click rate
                'submitted' => 8, // 8% submit rate
            ],
            'awareness' => [
                'sent' => 100,
                'opened' => 85,
                'clicked' => 40,
                'submitted' => 15,
            ],
            'training' => [
                'sent' => 100,
                'opened' => 90,
                'clicked' => 60,
                'submitted' => 30,
            ],
            default => [
                'sent' => 100,
                'opened' => 75,
                'clicked' => 25,
                'submitted' => 8,
            ],
        };

        // Adjust rates based on department (simulate different vulnerability levels)
        $departmentMultipliers = [
            'IT' => 0.8,        // IT staff are more security-aware
            'Security' => 0.6,  // Security team is most aware
            'Executive' => 1.2, // Executives are often targeted
            'HR' => 1.1,        // HR staff handle sensitive info
            'Finance' => 1.3,   // Finance is high-value target
            'default' => 1.0,
        ];

        $multiplier = $departmentMultipliers[$target->department] ?? $departmentMultipliers['default'];

        // Determine which interactions occurred
        $interactions = ['sent']; // Always sent

        if (rand(1, 100) <= ($baseRates['opened'] * $multiplier)) {
            $interactions[] = 'opened';
        }

        if (in_array('opened', $interactions) && rand(1, 100) <= ($baseRates['clicked'] * $multiplier)) {
            $interactions[] = 'clicked';
        }

        if (in_array('clicked', $interactions) && rand(1, 100) <= ($baseRates['submitted'] * $multiplier)) {
            $interactions[] = 'submitted';
        }

        return count($interactions);
    }

    /**
     * Generate timestamp for interaction
     */
    private function generateTimestamp(Carbon $baseTime, int $index, string $actionType): Carbon
    {
        $timeIntervals = [
            'sent' => 0,        // Sent immediately
            'opened' => 30,     // Opened within 30 minutes
            'clicked' => 60,    // Clicked within 1 hour
            'submitted' => 120, // Submitted within 2 hours
        ];

        $minutesToAdd = $timeIntervals[$actionType] + ($index * 15); // Add some randomness
        
        return $baseTime->copy()->addMinutes($minutesToAdd);
    }
}

