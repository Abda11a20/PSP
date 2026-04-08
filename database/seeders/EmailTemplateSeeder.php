<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Password Reset Phishing',
                'type' => 'phishing',
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>Password Reset Required</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #d32f2f;">Security Alert: Password Reset Required</h2>
                            <p>Dear Employee,</p>
                            <p>We have detected suspicious activity on your account. For your security, we need you to reset your password immediately.</p>
                            <p>Click the button below to reset your password:</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{reset_link}}" style="background-color: #d32f2f; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Reset Password</a>
                            </div>
                            <p><strong>Important:</strong> This link will expire in 24 hours for security reasons.</p>
                            <p>If you did not request this password reset, please contact IT support immediately.</p>
                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                            <p style="font-size: 12px; color: #666;">This is a security awareness test. Please report this email to your IT department.</p>
                        </div>
                    </body>
                    </html>
                ',
            ],
            [
                'name' => 'IT Support Scam',
                'type' => 'phishing',
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>IT Support Request</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #1976d2;">IT Support - Account Verification Required</h2>
                            <p>Hello,</p>
                            <p>Our IT department is performing routine security maintenance on all employee accounts. We need to verify your account information to ensure continued access.</p>
                            <p>Please click the link below to verify your account:</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{verification_link}}" style="background-color: #1976d2; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Verify Account</a>
                            </div>
                            <p>This verification is required to maintain your access to company systems.</p>
                            <p>If you have any questions, please contact IT support.</p>
                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                            <p style="font-size: 12px; color: #666;">This is a security awareness test. Please report this email to your IT department.</p>
                        </div>
                    </body>
                    </html>
                ',
            ],
            [
                'name' => 'Invoice Payment Request',
                'type' => 'phishing',
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>Invoice Payment Required</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #f57c00;">Invoice Payment Required</h2>
                            <p>Dear {{employee_name}},</p>
                            <p>We have an outstanding invoice that requires immediate attention. Please review and process the payment as soon as possible.</p>
                            <p><strong>Invoice Details:</strong></p>
                            <ul>
                                <li>Invoice #: INV-2024-001</li>
                                <li>Amount: $2,500.00</li>
                                <li>Due Date: {{due_date}}</li>
                            </ul>
                            <p>Click below to view and process the invoice:</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{invoice_link}}" style="background-color: #f57c00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">View Invoice</a>
                            </div>
                            <p>Please process this payment promptly to avoid any service interruptions.</p>
                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
                            <p style="font-size: 12px; color: #666;">This is a security awareness test. Please report this email to your IT department.</p>
                        </div>
                    </body>
                    </html>
                ',
            ],
            [
                'name' => 'Security Awareness Training',
                'type' => 'awareness',
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>Security Awareness Training</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #388e3c;">Security Awareness Training Required</h2>
                            <p>Dear {{employee_name}},</p>
                            <p>As part of our ongoing commitment to cybersecurity, all employees are required to complete the quarterly security awareness training.</p>
                            <p><strong>Training Details:</strong></p>
                            <ul>
                                <li>Duration: 30 minutes</li>
                                <li>Deadline: {{deadline}}</li>
                                <li>Topics: Phishing, Password Security, Data Protection</li>
                            </ul>
                            <p>Click below to access your training:</p>
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{training_link}}" style="background-color: #388e3c; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">Start Training</a>
                            </div>
                            <p>Completion of this training is mandatory for all employees.</p>
                            <p>If you have any questions, please contact the HR department.</p>
                        </div>
                    </body>
                    </html>
                ',
            ],
            [
                'name' => 'System Maintenance Notice',
                'type' => 'training',
                'html_content' => '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="utf-8">
                        <title>System Maintenance Notice</title>
                    </head>
                    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                            <h2 style="color: #7b1fa2;">Scheduled System Maintenance</h2>
                            <p>Dear {{employee_name}},</p>
                            <p>We will be performing scheduled maintenance on our systems to improve security and performance.</p>
                            <p><strong>Maintenance Details:</strong></p>
                            <ul>
                                <li>Date: {{maintenance_date}}</li>
                                <li>Time: 2:00 AM - 6:00 AM EST</li>
                                <li>Affected Systems: Email, VPN, File Servers</li>
                            </ul>
                            <p>During this time, some services may be temporarily unavailable. Please plan accordingly.</p>
                            <p>We apologize for any inconvenience this may cause.</p>
                            <p>For updates, please check our status page: <a href="{{status_link}}">System Status</a></p>
                        </div>
                    </body>
                    </html>
                ',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
}
