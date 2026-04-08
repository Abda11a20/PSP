<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in order to respect foreign key constraints
        $this->call([
            PlanSeeder::class,
            EmailTemplateSeeder::class,
            CompanySeeder::class,
            UserSeeder::class,
            PaymentSeeder::class,
            CampaignSeeder::class,
            CampaignTargetSeeder::class,
            InteractionSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed successfully!');
    }
}
