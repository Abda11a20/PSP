<?php

namespace App\Services;

use App\Jobs\SendPhishingEmailJob;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\EmailTemplate;
use App\Models\Interaction;
use App\Services\TelegramService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Generate unique token links for each target and queue email jobs
     */
    public function sendCampaignEmails(Campaign $campaign): array
    {
        Log::info('📧 [EmailService] sendCampaignEmails started', [
            'campaign_id' => $campaign->id,
            'campaign_type' => $campaign->type,
            'campaign_status' => $campaign->status,
            'company_id' => $campaign->company_id
        ]);

        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Get campaign targets
            $targets = $campaign->targets;
            
            Log::info('📧 [EmailService] Targets retrieved', [
                'campaign_id' => $campaign->id,
                'targets_count' => $targets->count(),
                'target_emails' => $targets->pluck('email')->toArray()
            ]);
            
            if ($targets->isEmpty()) {
                Log::error('📧 [EmailService] No targets found', [
                    'campaign_id' => $campaign->id
                ]);
                throw new \Exception('No targets found for this campaign');
            }

            // Get email template - use selected template or fall back to type-based lookup
            if ($campaign->email_template_id) {
                $template = \App\Models\EmailTemplate::find($campaign->email_template_id);
                Log::info('📧 [EmailService] Using selected template', [
                    'campaign_id' => $campaign->id,
                    'template_id' => $campaign->email_template_id,
                    'template_found' => $template ? true : false
                ]);
            } else {
                $template = $this->getEmailTemplate($campaign->type);
                Log::info('📧 [EmailService] Using type-based template lookup', [
                    'campaign_id' => $campaign->id,
                    'campaign_type' => $campaign->type,
                    'template_found' => $template ? true : false
                ]);
            }
            
            Log::info('📧 [EmailService] Template lookup result', [
                'campaign_id' => $campaign->id,
                'campaign_type' => $campaign->type,
                'template_id' => $template ? $template->id : null,
                'template_name' => $template ? $template->name : null,
                'template_found' => $template ? true : false
            ]);
            
            if (!$template) {
                Log::error('📧 [EmailService] Template not found', [
                    'campaign_id' => $campaign->id,
                    'campaign_type' => $campaign->type,
                    'email_template_id' => $campaign->email_template_id
                ]);
                throw new \Exception('Email template not found for campaign type: ' . $campaign->type);
            }

            // Process each target
            Log::info('📧 [EmailService] Starting to process targets', [
                'campaign_id' => $campaign->id,
                'total_targets' => $targets->count()
            ]);

            foreach ($targets as $index => $target) {
                try {
                    Log::info('📧 [EmailService] Processing target', [
                        'campaign_id' => $campaign->id,
                        'target_index' => $index + 1,
                        'target_id' => $target->id,
                        'target_email' => $target->email,
                        'target_name' => $target->name
                    ]);

                    $this->processTarget($campaign, $target, $template);
                    $results['success']++;
                    
                    Log::info('📧 [EmailService] Target processed successfully', [
                        'campaign_id' => $campaign->id,
                        'target_email' => $target->email,
                        'success_count' => $results['success']
                    ]);
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'target_email' => $target->email,
                        'error' => $e->getMessage()
                    ];
                    Log::error('📧 [EmailService] Failed to process target', [
                        'campaign_id' => $campaign->id,
                        'target_email' => $target->email,
                        'target_id' => $target->id,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'failed_count' => $results['failed']
                    ]);
                }
            }

            Log::info('📧 [EmailService] All targets processed', [
                'campaign_id' => $campaign->id,
                'success_count' => $results['success'],
                'failed_count' => $results['failed'],
                'total_targets' => $targets->count()
            ]);

            // Update campaign status
            $oldStatus = $campaign->status;
            $campaign->update(['status' => 'sent']);
            
            Log::info('📧 [EmailService] Campaign status updated', [
                'campaign_id' => $campaign->id,
                'old_status' => $oldStatus,
                'new_status' => 'sent'
            ]);

        } catch (\Exception $e) {
            Log::error('📧 [EmailService] Exception in sendCampaignEmails', [
                'campaign_id' => $campaign->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        Log::info('📧 [EmailService] sendCampaignEmails completed', [
            'campaign_id' => $campaign->id,
            'results' => $results
        ]);

        return $results;
    }

    /**
     * Process individual target and queue email job
     */
    protected function processTarget(Campaign $campaign, CampaignTarget $target, EmailTemplate $template): void
    {
        Log::info('📧 [EmailService] processTarget started', [
            'campaign_id' => $campaign->id,
            'target_id' => $target->id,
            'target_email' => $target->email,
            'target_name' => $target->name,
            'template_id' => $template->id,
            'template_name' => $template->name
        ]);

        // Generate unique token for this target
        $uniqueToken = $this->generateUniqueToken($campaign->id, $target->id);
        
        Log::info('📧 [EmailService] Token generated', [
            'campaign_id' => $campaign->id,
            'target_email' => $target->email,
            'token' => $uniqueToken
        ]);
        
        // Create interaction record
        try {
            $interaction = Interaction::create([
                'campaign_id' => $campaign->id,
                'email' => $target->email,
                'action_type' => 'sent',
                'timestamp' => now(),
            ]);

            Log::info('📧 [EmailService] Interaction record created', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'interaction_id' => $interaction->id,
                'action_type' => 'sent'
            ]);
        } catch (\Exception $e) {
            Log::error('📧 [EmailService] Failed to create interaction record', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Queue the email job
        try {
            Log::info('📧 [EmailService] Dispatching email job', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'queue_connection' => config('queue.default'),
                'queue_driver' => config('queue.connections.' . config('queue.default') . '.driver')
            ]);

            SendPhishingEmailJob::dispatch(
                $target->email,
                $target->name,
                $campaign,
                $template,
                $uniqueToken,
                $interaction->id
            );

            Log::info('📧 [EmailService] Email job dispatched successfully', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'interaction_id' => $interaction->id,
                'token' => $uniqueToken,
                'queue_connection' => config('queue.default')
            ]);
        } catch (\Exception $e) {
            Log::error('📧 [EmailService] Failed to dispatch email job', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'interaction_id' => $interaction->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique token for tracking
     */
    protected function generateUniqueToken(int $campaignId, int $targetId): string
    {
        $timestamp = now()->timestamp;
        $randomString = Str::random(16);
        
        return "{$campaignId}_{$targetId}_{$timestamp}_{$randomString}";
    }

    /**
     * Get email template based on campaign type
     */
    protected function getEmailTemplate(string $campaignType): ?EmailTemplate
    {
        return EmailTemplate::where('type', $campaignType)->first();
    }

    /**
     * Track email interaction (opened, clicked, submitted)
     */
    public function trackInteraction(string $token, string $actionType, array $metadata = null): array
    {
        try {
            // Parse token to get campaign and target info
            $tokenData = $this->parseToken($token);
            
            if (!$tokenData) {
                throw new \Exception('Invalid token');
            }

            // For submitted actions, find existing or create new
            // For other actions, update the most recent interaction for this email
            if ($actionType === 'submitted') {
                // Create a new interaction record for each submission
                $interaction = Interaction::create([
                    'campaign_id' => $tokenData['campaign_id'],
                    'email' => $tokenData['email'],
                    'action_type' => $actionType,
                    'timestamp' => now(),
                    'metadata' => $metadata,
                ]);

                // Send Telegram notification for high-risk alert
                try {
                    $campaign = Campaign::with('company')->find($tokenData['campaign_id']);
                    if ($campaign && $campaign->company) {
                        $company = $campaign->company;
                        if ($company->telegram_enabled) {
                            $target = CampaignTarget::where('campaign_id', $tokenData['campaign_id'])
                                ->where('email', $tokenData['email'])
                                ->first();
                            
                            $telegramService = app(TelegramService::class);
                            $message = $telegramService->formatCampaignMessage('high_risk', [
                                'campaign_name' => $campaign->type ?? 'Unknown',
                                'employee_name' => $target->name ?? 'Unknown',
                                'employee_email' => $tokenData['email'],
                                'time' => now()->format('Y-m-d H:i:s'),
                            ]);
                            $telegramService->sendMessage($company, $message);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send Telegram notification for high-risk alert', [
                        'error' => $e->getMessage(),
                        'campaign_id' => $tokenData['campaign_id'] ?? null,
                    ]);
                }
            } else {
                // Find or create interaction record for other actions
                $interaction = Interaction::where('campaign_id', $tokenData['campaign_id'])
                    ->where('email', $tokenData['email'])
                    ->where('action_type', $actionType)
                    ->first();

                if (!$interaction) {
                    // Create new interaction record if it doesn't exist
                    $interaction = Interaction::create([
                        'campaign_id' => $tokenData['campaign_id'],
                        'email' => $tokenData['email'],
                        'action_type' => $actionType,
                        'timestamp' => now(),
                        'metadata' => $metadata,
                    ]);
                } else {
                    // Update existing interaction with new timestamp
                    $interaction->update([
                        'timestamp' => now(),
                        'metadata' => $metadata ? array_merge($interaction->metadata ?? [], $metadata) : $interaction->metadata,
                    ]);
                }
            }

            // Log the interaction
            Log::info('Email interaction tracked', [
                'token' => $token,
                'action_type' => $actionType,
                'campaign_id' => $tokenData['campaign_id'],
                'email' => $tokenData['email']
            ]);

            return [
                'success' => true,
                'message' => 'Interaction tracked successfully',
                'interaction' => $interaction
            ];

        } catch (\Exception $e) {
            Log::error('Failed to track interaction', [
                'token' => $token,
                'action_type' => $actionType,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse token to extract campaign and target information
     */
    protected function parseToken(string $token): ?array
    {
        try {
            $parts = explode('_', $token);
            
            if (count($parts) < 4) {
                return null;
            }

            $campaignId = (int) $parts[0];
            $targetId = (int) $parts[1];
            
            // Get target email from database
            $target = CampaignTarget::find($targetId);
            
            if (!$target || $target->campaign_id !== $campaignId) {
                return null;
            }

            return [
                'campaign_id' => $campaignId,
                'target_id' => $targetId,
                'email' => $target->email,
                'timestamp' => $parts[2],
                'random' => $parts[3]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse token', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats(Campaign $campaign): array
    {
        $interactions = $campaign->interactions;
        $targets = $campaign->targets;
        
        $totalSent = $interactions->where('action_type', 'sent')->count();
        $totalOpened = $interactions->where('action_type', 'opened')->count();
        $totalClicked = $interactions->where('action_type', 'clicked')->count();
        $totalSubmitted = $interactions->where('action_type', 'submitted')->count();
        $totalFailed = $interactions->where('action_type', 'failed')->count();
        $totalTargets = $targets->count();
        
        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;
        $submitRate = $totalSent > 0 ? round(($totalSubmitted / $totalSent) * 100, 2) : 0;
        $vulnerabilityRate = $totalTargets > 0 ? round(($totalSubmitted / $totalTargets) * 100, 2) : 0;
        
        // Get vulnerable employees
        $vulnerableEmployees = [];
        foreach ($targets as $target) {
            $targetInteractions = $interactions->where('email', $target->email);
            $hasSubmitted = $targetInteractions->where('action_type', 'submitted')->count() > 0;
            $hasClicked = $targetInteractions->where('action_type', 'clicked')->count() > 0;
            $hasOpened = $targetInteractions->where('action_type', 'opened')->count() > 0;
            
            if ($hasSubmitted || $hasClicked || $hasOpened) {
                $riskLevel = $hasSubmitted ? 'high' : ($hasClicked ? 'medium' : 'low');
                $actions = [];
                if ($hasOpened) $actions[] = 'opened';
                if ($hasClicked) $actions[] = 'clicked';
                if ($hasSubmitted) $actions[] = 'submitted';
                
                $vulnerableEmployees[] = [
                    'name' => $target->name,
                    'email' => $target->email,
                    'risk_level' => $riskLevel,
                    'actions' => $actions,
                ];
            }
        }
        
        $stats = [
            'total_targets' => $totalTargets,
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'total_submitted' => $totalSubmitted,
            'total_failed' => $totalFailed,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'submit_rate' => $submitRate,
            'vulnerability_rate' => $vulnerabilityRate,
            'vulnerable_employees' => $vulnerableEmployees,
        ];

        return $stats;
    }

    /**
     * Resend email to specific target
     */
    public function resendEmail(Campaign $campaign, CampaignTarget $target): array
    {
        Log::info('📧 [EmailService] resendEmail started', [
            'campaign_id' => $campaign->id,
            'target_id' => $target->id,
            'target_email' => $target->email
        ]);

        try {
            // Get email template - use selected template or fall back to type-based lookup
            if ($campaign->email_template_id) {
                $template = \App\Models\EmailTemplate::find($campaign->email_template_id);
            } else {
                $template = $this->getEmailTemplate($campaign->type);
            }
            
            Log::info('📧 [EmailService] Template lookup for resend', [
                'campaign_id' => $campaign->id,
                'campaign_type' => $campaign->type,
                'email_template_id' => $campaign->email_template_id,
                'template_found' => $template ? true : false
            ]);
            
            if (!$template) {
                Log::error('📧 [EmailService] Template not found for resend', [
                    'campaign_id' => $campaign->id,
                    'campaign_type' => $campaign->type
                ]);
                throw new \Exception('Email template not found');
            }

            $this->processTarget($campaign, $target, $template);

            Log::info('📧 [EmailService] Resend email processed successfully', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'queue_connection' => config('queue.default')
            ]);

            $queueConnection = config('queue.default');
            $message = $queueConnection === 'sync' 
                ? 'Email sent successfully!' 
                : 'Email queued for resending';

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (\Exception $e) {
            Log::error('📧 [EmailService] Resend email failed', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cancel pending emails for a campaign
     */
    public function cancelCampaignEmails(Campaign $campaign): array
    {
        try {
            // In a real implementation, you would cancel queued jobs
            // For now, we'll just update the campaign status
            $campaign->update(['status' => 'cancelled']);

            Log::info('Campaign emails cancelled', [
                'campaign_id' => $campaign->id
            ]);

            return [
                'success' => true,
                'message' => 'Campaign emails cancelled successfully'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}