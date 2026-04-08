<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test 
                            {email? : The email address to send the test email to}
                            {--subject= : Custom subject for the test email}
                            {--html : Send as HTML email}
                            {--test-connection : Test network connectivity to SMTP server}
                            {--use-log : Temporarily use log driver for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📧 Testing Mail Configuration...');
        $this->newLine();

        // Test network connectivity if requested
        if ($this->option('test-connection')) {
            $this->testNetworkConnection();
            return 0;
        }

        // Display current mail configuration
        $this->displayMailConfig();
        
        // Use log driver if requested
        if ($this->option('use-log')) {
            $this->warn('⚠️  Using log driver for testing. Email will be written to logs instead of being sent.');
            config(['mail.default' => 'log']);
            $this->newLine();
        }

        // Get recipient email
        $email = $this->argument('email') ?? $this->ask('Enter recipient email address', config('mail.from.address'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Invalid email address: ' . $email);
            return 1;
        }

        // Get subject
        $subject = $this->option('subject') ?? 'Test Email from ' . config('app.name');

        $this->newLine();
        $this->info('Sending test email to: ' . $email);
        $this->info('Subject: ' . $subject);
        $this->newLine();

        try {
            if ($this->option('html')) {
                $this->sendHtmlEmail($email, $subject);
            } else {
                $this->sendPlainEmail($email, $subject);
            }

            $this->newLine();
            $this->info('✅ Test email sent successfully!');
            $this->line('Check your inbox or mail logs to verify the email was received.');
            
            // Show where to check based on mail driver
            $mailer = config('mail.default');
            if ($mailer === 'log') {
                $this->line('💡 Since you\'re using the "log" driver, check: storage/logs/laravel.log');
            } elseif ($mailer === 'array') {
                $this->line('💡 Since you\'re using the "array" driver, emails are stored in memory only.');
            }

            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            // Provide specific help based on error
            $errorMessage = strtolower($e->getMessage());
            $host = config('mail.mailers.smtp.host');
            
            if (str_contains($errorMessage, 'connection') || str_contains($errorMessage, 'unable to connect')) {
                $this->line('💡 Connection Error - Check the following:');
                $this->newLine();
                
                if (str_contains($host, 'gmail.com')) {
                    $this->line('📧 Gmail Configuration Requirements:');
                    $this->line('   1. Enable 2-Step Verification on your Google account');
                    $this->line('   2. Generate an App Password:');
                    $this->line('      - Go to: https://myaccount.google.com/apppasswords');
                    $this->line('      - Create an app password for "Mail"');
                    $this->line('      - Use that 16-character password (not your regular password)');
                    $this->line('   3. Make sure your .env has:');
                    $this->line('      MAIL_ENCRYPTION=tls');
                    $this->line('      MAIL_PORT=587');
                    $this->line('      MAIL_PASSWORD=<your-app-password>');
                    $this->newLine();
                    $this->line('   Alternative: Try port 465 with SSL:');
                    $this->line('      MAIL_PORT=465');
                    $this->line('      MAIL_ENCRYPTION=ssl');
                    $this->newLine();
                }
                
                $this->line('   4. Test network connectivity:');
                $this->line('      php artisan mail:test --test-connection');
                $this->newLine();
                $this->line('   5. Check Windows Firewall:');
                $this->line('      - Windows may be blocking outbound SMTP connections');
                $this->line('      - Try temporarily disabling firewall to test');
                $this->line('      - Or add exception for PHP/Artisan');
                $this->newLine();
                $this->line('   6. Test with log driver first:');
                $this->line('      php artisan mail:test ' . ($this->argument('email') ?: 'test@example.com') . ' --use-log');
                $this->line('      Then check: storage/logs/laravel.log');
                $this->newLine();
                $this->line('   7. Verify SMTP host and port are correct');
            } else {
                $this->line('💡 Make sure your mail configuration in .env is correct:');
                $this->line('   - MAIL_MAILER=smtp');
                $this->line('   - MAIL_HOST=' . ($host ?: 'smtp.gmail.com'));
                $this->line('   - MAIL_PORT=587');
                $this->line('   - MAIL_USERNAME=your-email@gmail.com');
                $this->line('   - MAIL_PASSWORD=your-app-password');
                $this->line('   - MAIL_ENCRYPTION=tls');
                $this->line('   - MAIL_FROM_ADDRESS=your-email@gmail.com');
                $this->line('   - MAIL_FROM_NAME="Your App Name"');
            }
            
            $this->newLine();
            $this->line('💡 After updating .env, run: php artisan config:clear');
            
            return 1;
        }
    }

    /**
     * Display current mail configuration
     */
    protected function displayMailConfig()
    {
        $this->line('Current Mail Configuration:');
        $this->line('─────────────────────────');
        $encryption = config('mail.mailers.smtp.encryption');
        $password = config('mail.mailers.smtp.password');
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Mailer', config('mail.default')],
                ['Host', config('mail.mailers.smtp.host') ?: 'N/A'],
                ['Port', config('mail.mailers.smtp.port') ?: 'N/A'],
                ['Encryption', $encryption ?: 'N/A (Required for Gmail!)'],
                ['Username', config('mail.mailers.smtp.username') ?: 'N/A'],
                ['Password', $password ? '***' . substr($password, -4) : 'Not set'],
                ['From Address', config('mail.from.address')],
                ['From Name', config('mail.from.name')],
            ]
        );
        
        // Show warnings for common issues
        if (config('mail.default') === 'smtp') {
            $host = config('mail.mailers.smtp.host');
            if (str_contains($host, 'gmail.com') && !$encryption) {
                $this->warn('⚠️  Gmail requires MAIL_ENCRYPTION=tls in your .env file!');
            }
            if (!$password) {
                $this->warn('⚠️  MAIL_PASSWORD is not set. Gmail requires an App Password!');
            }
        }
        $this->newLine();
    }

    /**
     * Send plain text email
     */
    protected function sendPlainEmail(string $email, string $subject)
    {
        $content = $this->getPlainEmailContent();

        Mail::raw($content, function ($message) use ($email, $subject) {
            $message->to($email)
                   ->subject($subject)
                   ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Send HTML email
     */
    protected function sendHtmlEmail(string $email, string $subject)
    {
        $htmlContent = $this->getHtmlEmailContent();

        Mail::html($htmlContent, function ($message) use ($email, $subject) {
            $message->to($email)
                   ->subject($subject)
                   ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Get plain text email content
     */
    protected function getPlainEmailContent(): string
    {
        return "This is a test email from " . config('app.name') . ".\n\n" .
               "If you received this email, your mail configuration is working correctly!\n\n" .
               "Mail Configuration:\n" .
               "- Mailer: " . config('mail.default') . "\n" .
               "- Host: " . (config('mail.mailers.smtp.host') ?: 'N/A') . "\n" .
               "- Port: " . (config('mail.mailers.smtp.port') ?: 'N/A') . "\n" .
               "- From: " . config('mail.from.address') . "\n\n" .
               "Sent at: " . now()->format('Y-m-d H:i:s') . "\n" .
               "Application: " . config('app.name');
    }

    /**
     * Get HTML email content
     */
    protected function getHtmlEmailContent(): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                .success { color: #4CAF50; font-weight: bold; }
                .info { background-color: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Mail Test Successful!</h1>
                </div>
                <div class='content'>
                    <p>This is a test email from <strong>" . config('app.name') . "</strong>.</p>
                    <p class='success'>If you received this email, your mail configuration is working correctly!</p>
                    
                    <div class='info'>
                        <h3>Mail Configuration:</h3>
                        <ul>
                            <li><strong>Mailer:</strong> " . config('mail.default') . "</li>
                            <li><strong>Host:</strong> " . (config('mail.mailers.smtp.host') ?: 'N/A') . "</li>
                            <li><strong>Port:</strong> " . (config('mail.mailers.smtp.port') ?: 'N/A') . "</li>
                            <li><strong>From:</strong> " . config('mail.from.address') . "</li>
                        </ul>
                    </div>
                    
                    <p><strong>Sent at:</strong> " . now()->format('Y-m-d H:i:s') . "</p>
                    <p><strong>Application:</strong> " . config('app.name') . "</p>
                </div>
                <div class='footer'>
                    <p>This is an automated test email from your Phishing Simulation Platform.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Test network connectivity to SMTP server
     */
    protected function testNetworkConnection()
    {
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port', 587);
        
        if (!$host) {
            $this->error('❌ MAIL_HOST is not configured in your .env file');
            return;
        }

        $this->info('🔍 Testing Network Connectivity...');
        $this->newLine();
        $this->line("Testing connection to: {$host}:{$port}");
        $this->newLine();

        // Test connection
        $startTime = microtime(true);
        $connection = @fsockopen($host, $port, $errno, $errstr, 10);
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2);

        if ($connection) {
            $this->info("✅ Successfully connected to {$host}:{$port}");
            $this->line("   Response time: {$responseTime}ms");
            fclose($connection);
            $this->newLine();
            $this->line('💡 Network connectivity is OK. The issue might be:');
            $this->line('   - Authentication credentials');
            $this->line('   - SSL/TLS handshake');
            $this->line('   - Gmail security settings');
        } else {
            $this->error("❌ Failed to connect to {$host}:{$port}");
            $this->line("   Error: {$errstr} (Code: {$errno})");
            $this->newLine();
            
            $this->line('💡 Possible causes:');
            $this->line('   1. Windows Firewall is blocking outbound connections');
            $this->line('   2. Your network/ISP is blocking SMTP ports');
            $this->line('   3. Antivirus software is blocking the connection');
            $this->line('   4. Corporate firewall/proxy restrictions');
            $this->newLine();
            
            $this->line('🔧 Try these solutions:');
            $this->line('   1. Temporarily disable Windows Firewall to test');
            $this->line('   2. Check if your antivirus has a firewall');
            $this->line('   3. Try using a different network (mobile hotspot)');
            $this->line('   4. Use Mailtrap or similar service for testing:');
            $this->line('      MAIL_HOST=smtp.mailtrap.io');
            $this->line('      MAIL_PORT=2525');
            $this->line('      MAIL_USERNAME=<mailtrap-username>');
            $this->line('      MAIL_PASSWORD=<mailtrap-password>');
            $this->line('      MAIL_ENCRYPTION=tls');
        }

        // Test alternative ports for Gmail
        if (str_contains($host, 'gmail.com')) {
            $this->newLine();
            $this->line('📧 Testing Gmail alternative ports...');
            $this->newLine();
            
            $ports = [
                ['port' => 465, 'encryption' => 'ssl', 'name' => 'SSL'],
                ['port' => 587, 'encryption' => 'tls', 'name' => 'TLS'],
            ];
            
            foreach ($ports as $portConfig) {
                $testPort = $portConfig['port'];
                $this->line("Testing port {$testPort} ({$portConfig['name']})...");
                $testConnection = @fsockopen($host, $testPort, $testErrno, $testErrstr, 5);
                
                if ($testConnection) {
                    $this->info("   ✅ Port {$testPort} is accessible");
                    fclose($testConnection);
                } else {
                    $this->error("   ❌ Port {$testPort} is blocked");
                }
            }
        }
    }
}

