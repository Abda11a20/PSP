<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\EmailTemplate;
use App\Models\Interaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting complete database seeding...');

        // Seed in order to respect foreign key constraints
        $this->seedPlans();
        $this->seedEmailTemplates();
        $this->seedCompanies();
        $this->seedUsers();
        $this->seedPayments();
        $this->seedCampaigns();
        $this->seedCampaignTargets();
        $this->seedInteractions();

        $this->command->info('✅ Complete database seeding finished!');
    }

    /**
     * Seed subscription plans
     */
    private function seedPlans(): void
    {
        $this->command->info('📋 Seeding subscription plans...');

        $plans = [
            [
                'name' => 'Free',
                'price' => 0.00,
                'employee_limit' => 10,
                'features' => json_encode([
                    'Basic phishing simulations',
                    'Email templates',
                    'Basic reporting',
                    'Community support'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic',
                'price' => 29.99,
                'employee_limit' => 50,
                'features' => json_encode([
                    'Advanced phishing simulations',
                    'Custom email templates',
                    'Detailed reporting',
                    'Email support',
                    'API access'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'price' => 79.99,
                'employee_limit' => 200,
                'features' => json_encode([
                    'All Basic features',
                    'AI-powered analysis',
                    'Advanced reporting',
                    'Priority support',
                    'Custom branding',
                    'Team management'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'price' => 199.99,
                'employee_limit' => 1000,
                'features' => json_encode([
                    'All Standard features',
                    'White-label solution',
                    'Custom integrations',
                    'Dedicated support',
                    'Advanced analytics',
                    'Compliance reporting'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'price' => 499.99,
                'employee_limit' => -1, // Unlimited
                'features' => json_encode([
                    'All Premium features',
                    'On-premise deployment',
                    'Custom development',
                    '24/7 support',
                    'SLA guarantee',
                    'Advanced security'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }

        $this->command->info('✅ Plans seeded successfully');
    }

    /**
     * Seed email templates
     */
    private function seedEmailTemplates(): void
    {
        $this->command->info('📧 Seeding email templates...');

        $templates = [
            [
                'name' => 'Password Reset Request',
                'type' => 'phishing',
                'subject' => 'Urgent: Password Reset Required',
                'html_content' => $this->getPasswordResetTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT Security Alert',
                'type' => 'phishing',
                'subject' => 'Security Alert: Immediate Action Required',
                'html_content' => $this->getSecurityAlertTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Invoice Payment',
                'type' => 'phishing',
                'subject' => 'Invoice #INV-2024-001 - Payment Required',
                'html_content' => $this->getInvoiceTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HR Benefits Update',
                'type' => 'phishing',
                'subject' => 'Important: Benefits Enrollment Deadline',
                'html_content' => $this->getHRBenefitsTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CEO Executive Update',
                'type' => 'phishing',
                'subject' => 'Executive Update: Q4 Strategic Initiatives',
                'html_content' => $this->getCEOUpdateTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Security Awareness Training',
                'type' => 'awareness',
                'subject' => 'Monthly Security Awareness Training',
                'html_content' => $this->getAwarenessTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phishing Simulation Results',
                'type' => 'training',
                'subject' => 'Your Phishing Simulation Results',
                'html_content' => $this->getTrainingTemplate(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }

        $this->command->info('✅ Email templates seeded successfully');
    }

    /**
     * Seed companies
     */
    private function seedCompanies(): void
    {
        $this->command->info('🏢 Seeding companies...');

        $companies = [
            [
                'name' => 'Acme Corporation',
                'email' => 'admin@acme.com',
                'password' => Hash::make('password123'),
                'plan_id' => 2, // Basic plan
                'created_at' => now()->subDays(30),
                'updated_at' => now(),
            ],
            [
                'name' => 'TechStart Inc',
                'email' => 'admin@techstart.com',
                'password' => Hash::make('password123'),
                'plan_id' => 3, // Standard plan
                'created_at' => now()->subDays(15),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Enterprises Ltd',
                'email' => 'admin@globalenterprises.com',
                'password' => Hash::make('password123'),
                'plan_id' => 4, // Premium plan
                'created_at' => now()->subDays(7),
                'updated_at' => now(),
            ],
            [
                'name' => 'StartupXYZ',
                'email' => 'admin@startupxyz.com',
                'password' => Hash::make('password123'),
                'plan_id' => 1, // Free plan
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
            [
                'name' => 'MegaCorp International',
                'email' => 'admin@megacorp.com',
                'password' => Hash::make('password123'),
                'plan_id' => 5, // Enterprise plan
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }

        $this->command->info('✅ Companies seeded successfully');
    }

    /**
     * Seed users
     */
    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');

        $companies = Company::all();
        $users = [];

        foreach ($companies as $company) {
            // Create admin user for each company
            $users[] = [
                'company_id' => $company->id,
                'name' => $company->name . ' Admin',
                'email' => $company->email,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => $company->created_at,
                'updated_at' => now(),
            ];

            // Create additional users for larger companies
            if ($company->plan_id >= 3) { // Standard and above
                for ($i = 1; $i <= 3; $i++) {
                    $users[] = [
                        'company_id' => $company->id,
                        'name' => 'User ' . $i . ' - ' . $company->name,
                        'email' => 'user' . $i . '@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
                        'password' => Hash::make('password123'),
                        'role' => 'user',
                        'is_active' => true,
                        'created_at' => $company->created_at->addDays($i),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ Users seeded successfully');
    }

    /**
     * Seed payments
     */
    private function seedPayments(): void
    {
        $this->command->info('💳 Seeding payments...');

        $companies = Company::where('plan_id', '>', 1)->get(); // Only paid plans

        foreach ($companies as $company) {
            $plan = Plan::find($company->plan_id);
            
            // Create initial payment
            Payment::create([
                'company_id' => $company->id,
                'plan_id' => $company->plan_id,
                'amount' => $plan->price,
                'status' => 'completed',
                'transaction_id' => 'txn_' . uniqid(),
                'created_at' => $company->created_at,
                'updated_at' => now(),
            ]);

            // Create recurring payments for older companies
            if ($company->created_at->lt(now()->subDays(15))) {
                for ($i = 1; $i <= 2; $i++) {
                    Payment::create([
                        'company_id' => $company->id,
                        'plan_id' => $company->plan_id,
                        'amount' => $plan->price,
                        'status' => 'completed',
                        'transaction_id' => 'txn_' . uniqid(),
                        'created_at' => $company->created_at->addDays($i * 30),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Payments seeded successfully');
    }

    /**
     * Seed campaigns
     */
    private function seedCampaigns(): void
    {
        $this->command->info('🎯 Seeding campaigns...');

        $companies = Company::all();
        $campaignTypes = ['phishing', 'awareness', 'training'];

        foreach ($companies as $company) {
            $campaignCount = rand(2, 8); // 2-8 campaigns per company

            for ($i = 1; $i <= $campaignCount; $i++) {
                $type = $campaignTypes[array_rand($campaignTypes)];
                $startDate = now()->subDays(rand(1, 60));
                $endDate = $startDate->copy()->addDays(rand(7, 30));

                $statuses = ['draft', 'active', 'completed', 'paused'];
                $status = $startDate->isFuture() ? 'draft' : 
                         ($endDate->isPast() ? 'completed' : 
                         ($i % 3 == 0 ? 'paused' : 'active'));

                Campaign::create([
                    'company_id' => $company->id,
                    'type' => $type,
                    'status' => $status,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'created_at' => $startDate->subDays(rand(1, 5)),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Campaigns seeded successfully');
    }

    /**
     * Seed campaign targets
     */
    private function seedCampaignTargets(): void
    {
        $this->command->info('👤 Seeding campaign targets...');

        $campaigns = Campaign::all();
        $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations', 'Legal', 'Executive'];

        foreach ($campaigns as $campaign) {
            $targetCount = rand(5, 25); // 5-25 targets per campaign

            for ($i = 1; $i <= $targetCount; $i++) {
                $department = $departments[array_rand($departments)];
                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();
                $email = strtolower($firstName . '.' . $lastName . '@' . strtolower(str_replace(' ', '', $campaign->company->name)) . '.com');

                CampaignTarget::create([
                    'campaign_id' => $campaign->id,
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'department' => $department,
                    'created_at' => $campaign->created_at,
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Campaign targets seeded successfully');
    }

    /**
     * Seed interactions
     */
    private function seedInteractions(): void
    {
        $this->command->info('📊 Seeding interactions...');

        $campaigns = Campaign::with('targets')->get();

        foreach ($campaigns as $campaign) {
            foreach ($campaign->targets as $target) {
                $interactionTypes = ['sent', 'opened', 'clicked', 'submitted'];
                $interactionCount = rand(1, 4); // 1-4 interactions per target
                $selectedInteractions = array_slice($interactionTypes, 0, $interactionCount);

                foreach ($selectedInteractions as $index => $actionType) {
                    $timestamp = $campaign->start_date->addMinutes(rand(5, 1440 * 7)); // Within campaign period

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
        }

        $this->command->info('✅ Interactions seeded successfully');
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
            'Sarah', 'Timothy', 'Kimberly', 'Ronald', 'Deborah', 'Jason', 'Dorothy'
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
            'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill'
        ];

        return $lastNames[array_rand($lastNames)];
    }

    /**
     * Password reset email template
     */
    private function getPasswordResetTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset Required</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #d32f2f;">🔒 Urgent: Password Reset Required</h2>
        <p>Hello {{name}},</p>
        <p>We have detected suspicious activity on your account and need to reset your password immediately for security purposes.</p>
        <p><strong>Action Required:</strong> Please click the link below to reset your password within the next 24 hours.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #d32f2f; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password Now</a>
        </div>
        <p><strong>Important:</strong> If you do not reset your password within 24 hours, your account will be temporarily suspended.</p>
        <p>If you did not request this password reset, please contact our IT department immediately.</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">This is an automated message. Please do not reply to this email.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * Security alert email template
     */
    private function getSecurityAlertTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Security Alert</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ff6b35;">⚠️ Security Alert: Immediate Action Required</h2>
        <p>Hello {{name}},</p>
        <p>Our security team has detected a potential security breach that may affect your account. We need you to verify your identity and update your security settings immediately.</p>
        <p><strong>What happened:</strong> Unusual login attempts were detected from an unrecognized device.</p>
        <p><strong>Action Required:</strong> Please verify your account by clicking the link below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #ff6b35; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Account</a>
        </div>
        <p><strong>Security Tips:</strong></p>
        <ul>
            <li>Never share your password with anyone</li>
            <li>Use strong, unique passwords</li>
            <li>Enable two-factor authentication</li>
        </ul>
        <p>If you did not attempt to log in recently, please contact our security team immediately.</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">IT Security Department<br>This is an automated security alert.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * Invoice payment email template
     */
    private function getInvoiceTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Payment Required</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2e7d32;">💰 Invoice Payment Required</h2>
        <p>Hello {{name}},</p>
        <p>This is a reminder that your invoice #INV-2024-001 is due for payment.</p>
        <div style="background-color: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3>Invoice Details:</h3>
            <p><strong>Invoice Number:</strong> INV-2024-001</p>
            <p><strong>Amount Due:</strong> $2,450.00</p>
            <p><strong>Due Date:</strong> ' . now()->addDays(7)->format('M d, Y') . '</p>
            <p><strong>Description:</strong> Monthly Software License</p>
        </div>
        <p>Please process payment by clicking the link below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #2e7d32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Pay Invoice</a>
        </div>
        <p>If you have already made this payment, please disregard this notice.</p>
        <p>For questions about this invoice, please contact our billing department.</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">Billing Department<br>This is an automated invoice reminder.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * HR benefits email template
     */
    private function getHRBenefitsTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Benefits Enrollment Deadline</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #1976d2;">📋 Important: Benefits Enrollment Deadline</h2>
        <p>Hello {{name}},</p>
        <p>This is a friendly reminder that the annual benefits enrollment period is ending soon. You have until ' . now()->addDays(3)->format('M d, Y') . ' to make your selections.</p>
        <p><strong>What you need to do:</strong></p>
        <ul>
            <li>Review your current benefits selections</li>
            <li>Make any necessary changes</li>
            <li>Submit your enrollment by the deadline</li>
        </ul>
        <p>Click the link below to access your benefits portal:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #1976d2; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Access Benefits Portal</a>
        </div>
        <p><strong>Important:</strong> If you do not make any changes, your current selections will remain in effect for the next year.</p>
        <p>If you have questions about your benefits, please contact HR at hr@company.com or call (555) 123-4567.</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">Human Resources Department<br>This is an automated benefits reminder.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * CEO executive update email template
     */
    private function getCEOUpdateTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #7b1fa2;">👔 Executive Update: Q4 Strategic Initiatives</h2>
        <p>Hello {{name}},</p>
        <p>I hope this message finds you well. As we approach the end of Q4, I wanted to share some important updates about our strategic initiatives and upcoming changes.</p>
        <p><strong>Key Updates:</strong></p>
        <ul>
            <li>New security protocols will be implemented company-wide</li>
            <li>Updated employee handbook with new policies</li>
            <li>Mandatory security training for all employees</li>
        </ul>
        <p>Please review the attached document and complete the required actions by clicking the link below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #7b1fa2; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Review Document</a>
        </div>
        <p>This is a confidential communication. Please do not share this information outside the organization.</p>
        <p>Thank you for your continued dedication to our company\'s success.</p>
        <p>Best regards,<br>CEO</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">Executive Office<br>Confidential Communication</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * Security awareness email template
     */
    private function getAwarenessTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Security Awareness Training</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #388e3c;">🛡️ Monthly Security Awareness Training</h2>
        <p>Hello {{name}},</p>
        <p>This month\'s security awareness training is now available. This training covers important topics to help keep our organization secure.</p>
        <p><strong>This month\'s topics include:</strong></p>
        <ul>
            <li>Recognizing phishing emails</li>
            <li>Password security best practices</li>
            <li>Social engineering awareness</li>
            <li>Data protection guidelines</li>
        </ul>
        <p>Please complete this training by the end of the month:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #388e3c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Start Training</a>
        </div>
        <p><strong>Training Duration:</strong> Approximately 15 minutes</p>
        <p><strong>Deadline:</strong> ' . now()->endOfMonth()->format('M d, Y') . '</p>
        <p>Completion of this training is mandatory for all employees and will be tracked in your employee record.</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">IT Security Training Department<br>This is an automated training reminder.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }

    /**
     * Training results email template
     */
    private function getTrainingTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Phishing Simulation Results</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #f57c00;">📊 Your Phishing Simulation Results</h2>
        <p>Hello {{name}},</p>
        <p>Thank you for participating in our recent phishing simulation. Here are your results and some important security tips.</p>
        <div style="background-color: #fff3e0; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3>Your Results:</h3>
            <p><strong>Simulation Date:</strong> ' . now()->subDays(rand(1, 7))->format('M d, Y') . '</p>
            <p><strong>Your Response:</strong> Clicked on the phishing link</p>
            <p><strong>Risk Level:</strong> Medium</p>
        </div>
        <p><strong>What to remember:</strong></p>
        <ul>
            <li>Always verify the sender\'s email address</li>
            <li>Look for spelling and grammar errors</li>
            <li>Hover over links before clicking</li>
            <li>When in doubt, contact IT support</li>
        </ul>
        <p>For more security tips and training resources, click below:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{fake_link}}" style="background-color: #f57c00; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">View Training Resources</a>
        </div>
        <p>Remember: Security is everyone\'s responsibility. Stay vigilant!</p>
        <hr style="margin: 30px 0;">
        <p style="font-size: 12px; color: #666;">IT Security Training Department<br>This is an automated training results email.</p>
        {{tracking_pixel}}
    </div>
</body>
</html>';
    }
}

