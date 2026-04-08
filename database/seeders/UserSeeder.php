<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Seeding users...');

        // Create users for each company
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createUsersForCompany($company);
        }

        // Create additional test users
        $this->createTestUsers();

        $this->command->info('✅ Users seeded successfully');
    }

    /**
     * Create users for a specific company
     */
    private function createUsersForCompany(Company $company): void
    {
        // Create admin user
        User::create([
            'company_id' => $company->id,
            'name' => $company->name . ' Admin',
            'email' => $company->email,
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'created_at' => $company->created_at,
            'updated_at' => now(),
        ]);

        // Create additional users based on company plan
        $userCount = $this->getUserCountForPlan($company->plan_id);

        for ($i = 1; $i <= $userCount; $i++) {
            $role = $i === 1 ? 'manager' : 'user';
            
            User::create([
                'company_id' => $company->id,
                'name' => $this->generateUserName($company->name, $i),
                'email' => $this->generateUserEmail($company->name, $i),
                'password' => Hash::make('password123'),
                'role' => $role,
                'is_active' => true,
                'created_at' => $company->created_at->addDays($i),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Create additional test users
     */
    private function createTestUsers(): void
    {
        $companies = Company::all();
        
        if ($companies->count() >= 3) {
            $testUsers = [
                [
                    'company_id' => $companies->where('name', 'like', '%Acme%')->first()?->id ?? $companies->first()->id,
                    'name' => 'John Smith',
                    'email' => 'john.smith@acme.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                [
                    'company_id' => $companies->where('name', 'like', '%Acme%')->first()?->id ?? $companies->first()->id,
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@acme.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                [
                    'company_id' => $companies->where('name', 'like', '%TechStart%')->first()?->id ?? $companies->skip(1)->first()?->id ?? $companies->first()->id,
                    'name' => 'Mike Chen',
                    'email' => 'mike.chen@techstart.com',
                    'password' => Hash::make('password123'),
                    'role' => 'manager',
                    'is_active' => true,
                ],
                [
                    'company_id' => $companies->where('name', 'like', '%TechStart%')->first()?->id ?? $companies->skip(1)->first()?->id ?? $companies->first()->id,
                    'name' => 'Emily Davis',
                    'email' => 'emily.davis@techstart.com',
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'is_active' => true,
                ],
                [
                    'company_id' => $companies->where('name', 'like', '%Global%')->first()?->id ?? $companies->skip(2)->first()?->id ?? $companies->first()->id,
                    'name' => 'Robert Wilson',
                    'email' => 'robert.wilson@globalenterprises.com',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'is_active' => true,
                ],
            ];

            foreach ($testUsers as $user) {
                if ($user['company_id']) {
                    User::create($user);
                }
            }
        }
    }

    /**
     * Get user count based on company plan
     */
    private function getUserCountForPlan(int $planId): int
    {
        return match ($planId) {
            1 => 2, // Free plan - 2 additional users
            2 => 5, // Basic plan - 5 additional users
            3 => 10, // Standard plan - 10 additional users
            4 => 20, // Premium plan - 20 additional users
            5 => 50, // Enterprise plan - 50 additional users
            default => 2,
        };
    }

    /**
     * Generate user name
     */
    private function generateUserName(string $companyName, int $index): string
    {
        $firstNames = [
            'Alex', 'Jordan', 'Taylor', 'Casey', 'Morgan', 'Riley', 'Avery', 'Quinn',
            'Blake', 'Cameron', 'Drew', 'Emery', 'Finley', 'Hayden', 'Jamie', 'Kendall',
            'Logan', 'Parker', 'Peyton', 'Reese', 'Sage', 'Skyler', 'Spencer', 'Sydney'
        ];

        $lastNames = [
            'Anderson', 'Brown', 'Clark', 'Davis', 'Evans', 'Foster', 'Garcia', 'Harris',
            'Jackson', 'Johnson', 'King', 'Lee', 'Miller', 'Moore', 'Nelson', 'Parker',
            'Roberts', 'Smith', 'Taylor', 'Thomas', 'Walker', 'White', 'Williams', 'Wilson'
        ];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        return $firstName . ' ' . $lastName;
    }

    /**
     * Generate user email
     */
    private function generateUserEmail(string $companyName, int $index): string
    {
        $companyDomain = strtolower(str_replace([' ', 'Inc', 'Ltd', 'Corp', 'Corporation'], '', $companyName)) . '.com';
        $firstName = strtolower($this->generateUserName($companyName, $index));
        $firstName = explode(' ', $firstName)[0];
        
        return $firstName . $index . '@' . $companyDomain;
    }
}
