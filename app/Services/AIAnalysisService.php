<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Interaction;
use App\Models\CampaignTarget;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    /**
     * Analyze campaign results and provide AI insights
     */
    public function analyzeCampaign(int $campaignId): array
    {
        try {
            $campaign = Campaign::with(['targets', 'interactions'])->findOrFail($campaignId);
            
            // Get current campaign statistics
            $currentStats = $this->getCampaignStatistics($campaign);
            
            // Get previous campaigns for comparison
            $previousCampaigns = $this->getPreviousCampaigns($campaign->company_id, $campaign->id);
            
            // Analyze performance
            $performanceAnalysis = $this->analyzePerformance($currentStats, $previousCampaigns);
            
            // Generate training suggestions
            $trainingSuggestions = $this->generateTrainingSuggestions($campaign);
            
            // Calculate improvement metrics
            $improvement = $this->calculateImprovement($currentStats, $previousCampaigns);
            
            // Generate risk assessment
            $riskAssessment = $this->assessRiskLevel($currentStats);
            
            // Create recommendations
            $recommendations = $this->generateRecommendations($currentStats, $performanceAnalysis);

            return [
                'campaign_id' => $campaignId,
                'campaign_type' => $campaign->type,
                'analysis_date' => now()->toISOString(),
                'current_performance' => $currentStats,
                'suggestions' => $trainingSuggestions,
                'improvement' => $improvement,
                'risk_level' => $riskAssessment,
                'recommendations' => $recommendations,
                'performance_analysis' => $performanceAnalysis,
            ];

        } catch (\Exception $e) {
            Log::error('AI Analysis failed', [
                'campaign_id' => $campaignId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Analysis failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get comprehensive campaign statistics
     */
    protected function getCampaignStatistics(Campaign $campaign): array
    {
        $interactions = $campaign->interactions;
        $targets = $campaign->targets;
        
        $totalTargets = $targets->count();
        $totalSent = $interactions->where('action_type', 'sent')->count();
        $totalOpened = $interactions->where('action_type', 'opened')->count();
        $totalClicked = $interactions->where('action_type', 'clicked')->count();
        $totalSubmitted = $interactions->where('action_type', 'submitted')->count();
        $totalFailed = $interactions->where('action_type', 'failed')->count();

        // Calculate rates
        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
        $clickRate = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;
        $submitRate = $totalSent > 0 ? round(($totalSubmitted / $totalSent) * 100, 2) : 0;

        // Get vulnerable employees (clicked or submitted)
        $vulnerableEmployees = $this->getVulnerableEmployees($campaign);

        return [
            'total_targets' => $totalTargets,
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'total_submitted' => $totalSubmitted,
            'total_failed' => $totalFailed,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'submit_rate' => $submitRate,
            'vulnerable_employees' => $vulnerableEmployees,
            'vulnerability_rate' => $totalTargets > 0 ? round((count($vulnerableEmployees) / $totalTargets) * 100, 2) : 0,
        ];
    }

    /**
     * Get previous campaigns for comparison
     */
    protected function getPreviousCampaigns(int $companyId, int $excludeCampaignId): array
    {
        $previousCampaigns = Campaign::where('company_id', $companyId)
            ->where('id', '!=', $excludeCampaignId)
            ->where('status', '!=', 'draft')
            ->with(['interactions', 'targets'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $campaignStats = [];
        foreach ($previousCampaigns as $campaign) {
            $stats = $this->getCampaignStatistics($campaign);
            $campaignStats[] = [
                'campaign_id' => $campaign->id,
                'type' => $campaign->type,
                'created_at' => $campaign->created_at,
                'stats' => $stats,
            ];
        }

        return $campaignStats;
    }

    /**
     * Get employees who clicked or submitted (vulnerable)
     */
    protected function getVulnerableEmployees(Campaign $campaign): array
    {
        $vulnerableEmails = $campaign->interactions
            ->whereIn('action_type', ['clicked', 'submitted'])
            ->pluck('email')
            ->unique()
            ->toArray();

        $vulnerableEmployees = [];
        foreach ($vulnerableEmails as $email) {
            $target = $campaign->targets->where('email', $email)->first();
            if ($target) {
                $interactions = $campaign->interactions->where('email', $email);
                $vulnerableEmployees[] = [
                    'name' => $target->name,
                    'email' => $email,
                    'actions' => $interactions->pluck('action_type')->toArray(),
                    'risk_level' => $this->calculateEmployeeRiskLevel($interactions),
                    'last_action' => $interactions->max('timestamp'),
                ];
            }
        }

        return $vulnerableEmployees;
    }

    /**
     * Calculate employee risk level
     */
    protected function calculateEmployeeRiskLevel($interactions): string
    {
        $actions = $interactions->pluck('action_type')->toArray();
        
        if (in_array('submitted', $actions)) {
            return 'high';
        } elseif (in_array('clicked', $actions)) {
            return 'medium';
        } elseif (in_array('opened', $actions)) {
            return 'low';
        }
        
        return 'none';
    }

    /**
     * Generate training suggestions
     */
    protected function generateTrainingSuggestions(Campaign $campaign): array
    {
        $suggestions = [];
        $vulnerableEmployees = $this->getVulnerableEmployees($campaign);
        
        if (empty($vulnerableEmployees)) {
            $suggestions[] = [
                'type' => 'success',
                'title' => 'Excellent Security Awareness',
                'description' => 'All employees demonstrated good security awareness by not clicking on suspicious links.',
                'priority' => 'low',
                'action_required' => false,
            ];
        } else {
            // High-risk employees (submitted)
            $highRiskEmployees = array_filter($vulnerableEmployees, fn($emp) => $emp['risk_level'] === 'high');
            if (!empty($highRiskEmployees)) {
                $suggestions[] = [
                    'type' => 'critical',
                    'title' => 'Immediate Security Training Required',
                    'description' => count($highRiskEmployees) . ' employee(s) submitted credentials to the phishing simulation. Immediate security training is required.',
                    'priority' => 'high',
                    'action_required' => true,
                    'employees' => array_column($highRiskEmployees, 'name'),
                    'training_modules' => [
                        'Phishing Recognition',
                        'Password Security',
                        'Social Engineering Awareness',
                        'Incident Reporting Procedures'
                    ],
                ];
            }

            // Medium-risk employees (clicked)
            $mediumRiskEmployees = array_filter($vulnerableEmployees, fn($emp) => $emp['risk_level'] === 'medium');
            if (!empty($mediumRiskEmployees)) {
                $suggestions[] = [
                    'type' => 'warning',
                    'title' => 'Additional Security Training Recommended',
                    'description' => count($mediumRiskEmployees) . ' employee(s) clicked on suspicious links. Additional training is recommended.',
                    'priority' => 'medium',
                    'action_required' => true,
                    'employees' => array_column($mediumRiskEmployees, 'name'),
                    'training_modules' => [
                        'Phishing Recognition',
                        'Link Verification',
                        'Email Security Best Practices'
                    ],
                ];
            }

            // General suggestions based on campaign type
            $suggestions[] = $this->getCampaignTypeSuggestions($campaign);
        }

        // Add general security recommendations
        $suggestions[] = [
            'type' => 'info',
            'title' => 'Ongoing Security Awareness',
            'description' => 'Continue regular phishing simulations and security awareness training to maintain a strong security culture.',
            'priority' => 'low',
            'action_required' => false,
        ];

        return $suggestions;
    }

    /**
     * Get campaign type specific suggestions
     */
    protected function getCampaignTypeSuggestions(Campaign $campaign): array
    {
        switch ($campaign->type) {
            case 'phishing':
                return [
                    'type' => 'info',
                    'title' => 'Phishing Simulation Results',
                    'description' => 'This phishing simulation tested employee awareness of suspicious emails. Review results with your security team.',
                    'priority' => 'medium',
                    'action_required' => false,
                ];
            case 'awareness':
                return [
                    'type' => 'info',
                    'title' => 'Security Awareness Training',
                    'description' => 'This awareness campaign provided educational content. Monitor employee engagement and follow up as needed.',
                    'priority' => 'low',
                    'action_required' => false,
                ];
            case 'training':
                return [
                    'type' => 'info',
                    'title' => 'Training Module Completion',
                    'description' => 'This training module assessed employee knowledge. Review completion rates and knowledge gaps.',
                    'priority' => 'medium',
                    'action_required' => false,
                ];
            default:
                return [
                    'type' => 'info',
                    'title' => 'Campaign Analysis',
                    'description' => 'Review campaign results and adjust future training accordingly.',
                    'priority' => 'low',
                    'action_required' => false,
                ];
        }
    }

    /**
     * Calculate improvement compared to previous campaigns
     */
    protected function calculateImprovement(array $currentStats, array $previousCampaigns): string
    {
        if (empty($previousCampaigns)) {
            return "This is your first campaign. Use this as a baseline for future comparisons.";
        }

        // Calculate average of previous campaigns
        $avgPreviousSubmitRate = collect($previousCampaigns)
            ->avg(fn($campaign) => $campaign['stats']['submit_rate']);
        
        $avgPreviousClickRate = collect($previousCampaigns)
            ->avg(fn($campaign) => $campaign['stats']['click_rate']);

        $currentSubmitRate = $currentStats['submit_rate'];
        $currentClickRate = $currentStats['click_rate'];

        // Calculate improvement (lower rates are better for phishing simulations)
        $submitImprovement = $avgPreviousSubmitRate - $currentSubmitRate;
        $clickImprovement = $avgPreviousClickRate - $currentClickRate;

        if ($submitImprovement > 5) {
            return "Excellent improvement! " . round($submitImprovement, 1) . "% fewer employees submitted credentials compared to previous campaigns.";
        } elseif ($submitImprovement > 0) {
            return "Good progress! " . round($submitImprovement, 1) . "% fewer employees submitted credentials compared to previous campaigns.";
        } elseif ($submitImprovement < -5) {
            return "Attention needed: " . round(abs($submitImprovement), 1) . "% more employees submitted credentials compared to previous campaigns.";
        } else {
            return "Performance is similar to previous campaigns. Consider additional training to improve results.";
        }
    }

    /**
     * Assess overall risk level
     */
    protected function assessRiskLevel(array $stats): array
    {
        $submitRate = $stats['submit_rate'];
        $clickRate = $stats['click_rate'];
        $vulnerabilityRate = $stats['vulnerability_rate'];

        if ($submitRate > 20 || $vulnerabilityRate > 50) {
            $level = 'high';
            $description = 'High risk: Many employees are vulnerable to phishing attacks. Immediate action required.';
        } elseif ($submitRate > 10 || $vulnerabilityRate > 30) {
            $level = 'medium';
            $description = 'Medium risk: Some employees are vulnerable. Additional training recommended.';
        } elseif ($submitRate > 5 || $vulnerabilityRate > 15) {
            $level = 'low';
            $description = 'Low risk: Most employees demonstrate good security awareness.';
        } else {
            $level = 'minimal';
            $description = 'Minimal risk: Excellent security awareness across the organization.';
        }

        return [
            'level' => $level,
            'description' => $description,
            'submit_rate' => $submitRate,
            'click_rate' => $clickRate,
            'vulnerability_rate' => $vulnerabilityRate,
        ];
    }

    /**
     * Analyze performance trends
     */
    protected function analyzePerformance(array $currentStats, array $previousCampaigns): array
    {
        if (empty($previousCampaigns)) {
            return [
                'trend' => 'baseline',
                'description' => 'This is the first campaign. No previous data for comparison.',
            ];
        }

        $avgPreviousSubmit = collect($previousCampaigns)->avg(fn($c) => $c['stats']['submit_rate']);
        $avgPreviousClick = collect($previousCampaigns)->avg(fn($c) => $c['stats']['click_rate']);

        $submitTrend = $currentStats['submit_rate'] < $avgPreviousSubmit ? 'improving' : 'declining';
        $clickTrend = $currentStats['click_rate'] < $avgPreviousClick ? 'improving' : 'declining';

        return [
            'trend' => $submitTrend,
            'description' => "Submit rate is {$submitTrend} compared to previous campaigns.",
            'submit_trend' => $submitTrend,
            'click_trend' => $clickTrend,
            'previous_average_submit' => round($avgPreviousSubmit, 2),
            'previous_average_click' => round($avgPreviousClick, 2),
        ];
    }

    /**
     * Generate actionable recommendations
     */
    protected function generateRecommendations(array $stats, array $performanceAnalysis): array
    {
        $recommendations = [];

        if ($stats['submit_rate'] > 15) {
            $recommendations[] = [
                'category' => 'training',
                'title' => 'Implement Mandatory Security Training',
                'description' => 'High submission rate indicates need for comprehensive security training program.',
                'priority' => 'high',
            ];
        }

        if ($stats['click_rate'] > 30) {
            $recommendations[] = [
                'category' => 'awareness',
                'title' => 'Enhance Phishing Awareness',
                'description' => 'High click rate suggests employees need better training on identifying suspicious links.',
                'priority' => 'medium',
            ];
        }

        if ($performanceAnalysis['trend'] === 'declining') {
            $recommendations[] = [
                'category' => 'strategy',
                'title' => 'Review Training Strategy',
                'description' => 'Performance is declining. Consider updating training methods and frequency.',
                'priority' => 'medium',
            ];
        }

        if ($stats['vulnerability_rate'] < 10) {
            $recommendations[] = [
                'category' => 'maintenance',
                'title' => 'Maintain Current Training Program',
                'description' => 'Excellent results! Continue current training program to maintain security awareness.',
                'priority' => 'low',
            ];
        }

        return $recommendations;
    }
}
