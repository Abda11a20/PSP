<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\EmailTemplate;
use App\Models\Interaction;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendPhishingEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 120; // Increased timeout for SMTP connections
    public $tries = 3;
    public $backoff = [10, 30, 60]; // Wait before retries

    protected $targetEmail;
    protected $targetName;
    protected $campaign;
    protected $template;
    protected $uniqueToken;
    protected $interactionId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $targetEmail,
        string $targetName,
        Campaign $campaign,
        EmailTemplate $template,
        string $uniqueToken,
        int $interactionId
    ) {
        $this->targetEmail = $targetEmail;
        $this->targetName = $targetName;
        $this->campaign = $campaign;
        $this->template = $template;
        $this->uniqueToken = $uniqueToken;
        $this->interactionId = $interactionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Verify models exist before processing (in case they were deleted)
        try {
            if (!$this->campaign || !$this->campaign->exists) {
                $campaignId = is_object($this->campaign) ? $this->campaign->id : $this->campaign;
                $this->campaign = Campaign::findOrFail($campaignId ?? 0);
            }
            if (!$this->template || !$this->template->exists) {
                $templateId = is_object($this->template) ? $this->template->id : $this->template;
                $this->template = EmailTemplate::findOrFail($templateId ?? 0);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('📧 [SendPhishingEmailJob] Model not found', [
                'campaign_id' => is_object($this->campaign) ? ($this->campaign->id ?? 'Unknown') : ($this->campaign ?? 'Unknown'),
                'template_id' => is_object($this->template) ? ($this->template->id ?? 'Unknown') : ($this->template ?? 'Unknown'),
                'target_email' => $this->targetEmail,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        Log::info('📧 [SendPhishingEmailJob] Job started', [
            'target_email' => $this->targetEmail,
            'target_name' => $this->targetName,
            'campaign_id' => $this->campaign->id,
            'template_id' => $this->template->id,
            'interaction_id' => $this->interactionId,
            'token' => $this->uniqueToken,
            'attempt' => $this->attempts(),
            'queue_connection' => config('queue.default')
        ]);

        try {
            // Prepare email content
            Log::info('📧 [SendPhishingEmailJob] Preparing email content', [
                'target_email' => $this->targetEmail,
                'campaign_id' => $this->campaign->id
            ]);

            $emailContent = $this->prepareEmailContent();
            
            Log::info('📧 [SendPhishingEmailJob] Email content prepared', [
                'target_email' => $this->targetEmail,
                'campaign_id' => $this->campaign->id,
                'subject' => $emailContent['subject'],
                'has_html_content' => !empty($emailContent['html_content']),
                'html_content_length' => strlen($emailContent['html_content'] ?? '')
            ]);
            
            // Send the email
            Log::info('📧 [SendPhishingEmailJob] Attempting to send email', [
                'target_email' => $this->targetEmail,
                'campaign_id' => $this->campaign->id,
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'mail_driver' => config('mail.default')
            ]);

            $this->sendEmail($emailContent);
            
            // Log successful send
            Log::info('📧 [SendPhishingEmailJob] Email sent successfully', [
                'target_email' => $this->targetEmail,
                'target_name' => $this->targetName,
                'campaign_id' => $this->campaign->id,
                'token' => $this->uniqueToken,
                'interaction_id' => $this->interactionId,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('📧 [SendPhishingEmailJob] Failed to send email', [
                'target_email' => $this->targetEmail,
                'target_name' => $this->targetName,
                'campaign_id' => $this->campaign->id,
                'token' => $this->uniqueToken,
                'interaction_id' => $this->interactionId,
                'attempt' => $this->attempts(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Prepare email content with personalized data
     */
    protected function prepareEmailContent(): array
    {
        Log::info('📧 [SendPhishingEmailJob] Preparing email content', [
            'target_email' => $this->targetEmail,
            'campaign_id' => $this->campaign->id,
            'template_id' => $this->template->id,
            'template_html_length' => strlen($this->template->html_content ?? '')
        ]);

        $fakeLink = $this->generateFakeLink();
        $trackingPixel = $this->generateTrackingPixel();
        
        Log::info('📧 [SendPhishingEmailJob] Generated links', [
            'target_email' => $this->targetEmail,
            'fake_link' => $fakeLink,
            'tracking_pixel' => $trackingPixel,
            'app_url' => config('app.url')
        ]);
        
        // Replace placeholders in template
        $htmlContent = $this->template->html_content;
        $htmlContent = str_replace('{{name}}', $this->targetName, $htmlContent);
        $htmlContent = str_replace('{{email}}', $this->targetEmail, $htmlContent);
        $htmlContent = str_replace('{{employee_name}}', $this->targetName, $htmlContent);
        $htmlContent = str_replace('{{fake_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{reset_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{login_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{verify_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{verification_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{training_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{status_link}}', $fakeLink, $htmlContent);
        $htmlContent = str_replace('{{deadline}}', now()->addDays(7)->format('F d, Y'), $htmlContent);
        $htmlContent = str_replace('{{maintenance_date}}', now()->addDays(3)->format('F d, Y'), $htmlContent);
        $htmlContent = str_replace('{{tracking_pixel}}', $trackingPixel, $htmlContent);
        $htmlContent = str_replace('{{campaign_name}}', $this->campaign->type, $htmlContent);

        $subject = $this->generateEmailSubject();

        Log::info('📧 [SendPhishingEmailJob] Email content prepared', [
            'target_email' => $this->targetEmail,
            'campaign_id' => $this->campaign->id,
            'subject' => $subject,
            'html_content_length' => strlen($htmlContent),
            'has_html_content' => !empty($htmlContent)
        ]);

        return [
            'subject' => $subject,
            'html_content' => $htmlContent,
            'fake_link' => $fakeLink,
            'tracking_pixel' => $trackingPixel,
        ];
    }

    /**
     * Generate fake link for phishing simulation
     */
    protected function generateFakeLink(): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/campaign/{$this->uniqueToken}";
    }

    /**
     * Generate tracking pixel for email opens
     */
    protected function generateTrackingPixel(): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/track/{$this->uniqueToken}/opened";
    }

    /**
     * Generate email subject based on campaign type
     */
    protected function generateEmailSubject(): string
    {
        $subjects = [
            'phishing' => [
                'Urgent: Verify Your Account Security',
                'Action Required: Suspicious Activity Detected',
                'Important: Update Your Password Immediately',
                'Security Alert: Unauthorized Login Attempt',
                'Account Verification Required'
            ],
            'awareness' => [
                'Security Training: Phishing Awareness',
                'Monthly Security Update',
                'Cybersecurity Best Practices',
                'Security Awareness Training',
                'Protect Your Digital Identity'
            ],
            'training' => [
                'Security Training Module Available',
                'Complete Your Security Training',
                'New Security Training Content',
                'Security Education Update',
                'Training Reminder: Cybersecurity'
            ]
        ];

        $campaignType = $this->campaign->type;
        $availableSubjects = $subjects[$campaignType] ?? $subjects['phishing'];
        
        return $availableSubjects[array_rand($availableSubjects)];
    }

    /**
     * Send the email using Laravel Mail
     */
    protected function sendEmail(array $emailContent): void
    {
        Log::info('📧 [SendPhishingEmailJob] sendEmail method called', [
            'target_email' => $this->targetEmail,
            'target_name' => $this->targetName,
            'subject' => $emailContent['subject'],
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_username' => config('mail.mailers.smtp.username') ? '***' : 'Not set'
        ]);

        try {
            Log::info('📧 [SendPhishingEmailJob] Preparing to send HTML email', [
                'target_email' => $this->targetEmail,
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'subject' => $emailContent['subject'],
                'html_content_length' => strlen($emailContent['html_content'] ?? ''),
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port')
            ]);

            // Test connection before attempting to send
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port', 587);
            
            if ($host && $port) {
                $connection = @fsockopen($host, $port, $errno, $errstr, 5);
                if (!$connection) {
                    Log::warning('📧 [SendPhishingEmailJob] Cannot connect to SMTP server', [
                        'host' => $host,
                        'port' => $port,
                        'error' => $errstr,
                        'error_code' => $errno,
                        'suggestion' => 'Check network connectivity, firewall, or use log driver for testing'
                    ]);
                } else {
                    fclose($connection);
                    Log::info('📧 [SendPhishingEmailJob] SMTP server connection test passed', [
                        'host' => $host,
                        'port' => $port
                    ]);
                }
            }

            // Use Mail::send() with HTML content
            Mail::send([], [], function ($message) use ($emailContent) {
                $message->to($this->targetEmail, $this->targetName)
                       ->subject($emailContent['subject'])
                       ->from(config('mail.from.address'), config('mail.from.name'))
                       ->html($emailContent['html_content']);
                
                Log::info('📧 [SendPhishingEmailJob] Mail message configured', [
                    'to' => $this->targetEmail,
                    'to_name' => $this->targetName,
                    'subject' => $emailContent['subject'],
                    'from' => config('mail.from.address'),
                    'from_name' => config('mail.from.name')
                ]);
            });

            Log::info('📧 [SendPhishingEmailJob] Mail sent successfully', [
                'target_email' => $this->targetEmail,
                'campaign_id' => $this->campaign->id
            ]);

        } catch (\Exception $e) {
            // Check if it's a transport/connection error
            $errorMessage = $e->getMessage();
            $isTransportError = str_contains($errorMessage, 'Connection') || 
                               str_contains($errorMessage, 'unable to connect') ||
                               str_contains($errorMessage, 'stream_socket_client') ||
                               str_contains($errorMessage, 'Transport');
            
            if ($isTransportError) {
                Log::error('📧 [SendPhishingEmailJob] SMTP Transport Exception', [
                    'target_email' => $this->targetEmail,
                    'campaign_id' => $this->campaign->id,
                    'error_message' => $errorMessage,
                    'error_class' => get_class($e),
                    'mail_host' => config('mail.mailers.smtp.host'),
                    'mail_port' => config('mail.mailers.smtp.port'),
                    'mail_encryption' => config('mail.mailers.smtp.encryption'),
                    'troubleshooting' => [
                        '1. Check network connectivity to SMTP server',
                        '2. Verify firewall is not blocking outbound connections',
                        '3. Test connection: php artisan mail:test --test-connection',
                        '4. For testing, use log driver: MAIL_MAILER=log in .env',
                        '5. Check if antivirus is blocking SMTP connections',
                        '6. Try different network (mobile hotspot) to test',
                        '7. Consider using Mailtrap for development'
                    ]
                ]);
            } else {
                Log::error('📧 [SendPhishingEmailJob] Exception in sendEmail', [
                    'target_email' => $this->targetEmail,
                    'campaign_id' => $this->campaign->id,
                    'error_message' => $errorMessage,
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'error_class' => get_class($e),
                    'mail_driver' => config('mail.default'),
                    'mail_host' => config('mail.mailers.smtp.host'),
                    'mail_port' => config('mail.mailers.smtp.port')
                ]);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('📧 [SendPhishingEmailJob] Exception in sendEmail', [
                'target_email' => $this->targetEmail,
                'campaign_id' => $this->campaign->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_class' => get_class($e),
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port')
            ]);
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('📧 [SendPhishingEmailJob] Job failed permanently', [
            'target_email' => $this->targetEmail,
            'target_name' => $this->targetName,
            'campaign_id' => $this->campaign->id,
            'token' => $this->uniqueToken,
            'interaction_id' => $this->interactionId,
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_trace' => $exception->getTraceAsString(),
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries
        ]);

        // Update interaction record to reflect failure
        if ($this->interactionId) {
            try {
                $interaction = Interaction::find($this->interactionId);
                if ($interaction) {
                    $interaction->update([
                        'action_type' => 'failed',
                        'timestamp' => now(),
                    ]);
                    Log::info('📧 [SendPhishingEmailJob] Interaction updated to failed', [
                        'interaction_id' => $this->interactionId,
                        'target_email' => $this->targetEmail
                    ]);
                } else {
                    Log::warning('📧 [SendPhishingEmailJob] Interaction not found for update', [
                        'interaction_id' => $this->interactionId,
                        'target_email' => $this->targetEmail
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('📧 [SendPhishingEmailJob] Failed to update interaction', [
                    'interaction_id' => $this->interactionId,
                    'target_email' => $this->targetEmail,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'campaign:' . $this->campaign->id,
            'email:' . $this->targetEmail,
            'type:phishing'
        ];
    }
}
