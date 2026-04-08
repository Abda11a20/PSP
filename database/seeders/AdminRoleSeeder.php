<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update Acme Corporation to admin role
        $company = Company::where('email', 'admin@acme.com')->first();
        if ($company) {
            $company->update(['role' => 'admin']);
            $this->command->info('Updated Acme Corporation to admin role');
        }
        
        // Create a developer account
        $developer = Company::where('email', 'developer@phishing-platform.com')->first();
        if (!$developer) {
            Company::create([
                'name' => 'Platform Developer',
                'email' => 'developer@phishing-platform.com',
                'password' => bcrypt('password123'),
                'plan_id' => 1, // Free plan
                'role' => 'developer',
            ]);
            $this->command->info('Created developer account');
        }
    }
}
