<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--force : Force installation even if .env exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Phishing Simulation Platform application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Installing Phishing Simulation Platform...');
        $this->newLine();




        // Step 2: Generate application key
        $this->generateAppKey();

        // Step 3: Run database migrations
        $this->runMigrations();

        // Step 4: Seed the database
        $this->seedDatabase();

        // Step 5: Create storage link
        $this->createStorageLink();

        // Step 6: Clear caches
        $this->clearCaches();

        $this->newLine();
        $this->info('✅ Installation completed successfully!');
        $this->newLine();
        $this->info('📝 Next steps:');
        $this->line('1. Update your .env file with your database credentials');
        $this->line('2. Configure your mail settings in .env');
        $this->line('3. Set up your AI service API keys');
        $this->line('4. Run: php artisan serve');
        $this->newLine();

        return 0;
    }

    private function createEnvFile()
    {
        $this->info('📄 Creating .env file...');
        
        $envContent = $this->getEnvTemplate();
        
        File::put(base_path('.env'), $envContent);
        
        $this->line('✅ .env file created');
    }

    private function generateAppKey()
    {
        $this->info('🔑 Generating application key...');
        
        Artisan::call('key:generate');
        
        $this->line('✅ Application key generated');
    }

    private function runMigrations()
    {
        $this->info('🗄️  Running database migrations...');
        
        try {
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->line('✅ Database migrations completed');
        } catch (\Exception $e) {
            $this->error('❌ Database migration failed: ' . $e->getMessage());
            $this->line('💡 Make sure your database is configured correctly in .env');
            throw $e;
        }
    }

    private function seedDatabase()
    {
        $this->info('🌱 Seeding database...');
        
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $this->line('✅ Database seeded successfully');
        } catch (\Exception $e) {
            $this->error('❌ Database seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createStorageLink()
    {
        $this->info('🔗 Creating storage link...');
        
        try {
            Artisan::call('storage:link');
            $this->line('✅ Storage link created');
        } catch (\Exception $e) {
            $this->warn('⚠️  Storage link creation failed: ' . $e->getMessage());
        }
    }

    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        
        $this->line('✅ Caches cleared');
    }

    private function getEnvTemplate()
    {
        return 'APP_NAME="Phishing Simulation Platform"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phishing_simulation
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# AI Service Configuration
OPENAI_API_KEY=
OPENAI_MODEL=gpt-3.5-turbo

# Payment Configuration
STRIPE_KEY=
STRIPE_SECRET=

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@phishingsim.com
MAIL_FROM_NAME="Phishing Simulation Platform"';
    }
}
