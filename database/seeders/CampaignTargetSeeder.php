<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignTarget;
use Illuminate\Database\Seeder;

class CampaignTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Seeding campaign targets...');

        $campaigns = Campaign::all();

        foreach ($campaigns as $campaign) {
            $this->createTargetsForCampaign($campaign);
        }

        $this->command->info('✅ Campaign targets seeded successfully');
    }

    /**
     * Create targets for a specific campaign
     */
    private function createTargetsForCampaign(Campaign $campaign): void
    {
        $targetCount = $this->getTargetCountForCampaign($campaign);
        $departments = $this->getDepartments();

        for ($i = 1; $i <= $targetCount; $i++) {
            $targetData = $this->generateTargetData($campaign, $departments, $i);
            CampaignTarget::create($targetData);
        }
    }

    /**
     * Get target count based on campaign type and company plan
     */
    private function getTargetCountForCampaign(Campaign $campaign): int
    {
        $baseCount = match ($campaign->type) {
            'phishing' => 15,
            'awareness' => 25,
            'training' => 30,
            default => 15,
        };

        // Adjust based on company plan
        $planMultiplier = match ($campaign->company->plan_id) {
            1 => 0.5, // Free plan - half targets
            2 => 1.0, // Basic plan - normal targets
            3 => 1.5, // Standard plan - 1.5x targets
            4 => 2.0, // Premium plan - 2x targets
            5 => 3.0, // Enterprise plan - 3x targets
            default => 1.0,
        };

        return (int) ($baseCount * $planMultiplier);
    }

    /**
     * Get departments list
     */
    private function getDepartments(): array
    {
        return [
            'IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations', 
            'Legal', 'Executive', 'Customer Service', 'Research & Development',
            'Quality Assurance', 'Administration', 'Security', 'Procurement'
        ];
    }

    /**
     * Generate target data
     */
    private function generateTargetData(Campaign $campaign, array $departments, int $index): array
    {
        $firstName = $this->getRandomFirstName();
        $lastName = $this->getRandomLastName();
        $department = $departments[array_rand($departments)];
        $email = $this->generateEmail($firstName, $lastName, $campaign->company->name);

        return [
            'campaign_id' => $campaign->id,
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'department' => $department,
            'created_at' => $campaign->created_at,
            'updated_at' => now(),
        ];
    }

    /**
     * Generate email address
     */
    private function generateEmail(string $firstName, string $lastName, string $companyName): string
    {
        $companyDomain = strtolower(str_replace([' ', 'Inc', 'Ltd', 'Corp', 'Corporation'], '', $companyName)) . '.com';
        $firstName = strtolower($firstName);
        $lastName = strtolower($lastName);
        
        return $firstName . '.' . $lastName . '@' . $companyDomain;
    }

    /**
     * Get random first name
     */
    private function getRandomFirstName(): string
    {
        $firstNames = [
            'John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Robert', 'Jessica',
            'William', 'Ashley', 'James', 'Amanda', 'Christopher', 'Jennifer', 'Daniel',
            'Lisa', 'Matthew', 'Nancy', 'Anthony', 'Karen', 'Mark', 'Betty', 'Donald',
            'Helen', 'Steven', 'Sandra', 'Paul', 'Donna', 'Andrew', 'Carol', 'Joshua',
            'Ruth', 'Kenneth', 'Sharon', 'Kevin', 'Michelle', 'Brian', 'Laura', 'George',
            'Sarah', 'Timothy', 'Kimberly', 'Ronald', 'Deborah', 'Jason', 'Dorothy',
            'Alex', 'Jordan', 'Taylor', 'Casey', 'Morgan', 'Riley', 'Avery', 'Quinn',
            'Blake', 'Cameron', 'Drew', 'Emery', 'Finley', 'Hayden', 'Jamie', 'Kendall'
        ];

        return $firstNames[array_rand($firstNames)];
    }

    /**
     * Get random last name
     */
    private function getRandomLastName(): string
    {
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson',
            'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson',
            'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker',
            'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill',
            'Flores', 'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell',
            'Mitchell', 'Carter', 'Roberts', 'Gomez', 'Phillips', 'Evans', 'Turner', 'Diaz'
        ];

        return $lastNames[array_rand($lastNames)];
    }
}

