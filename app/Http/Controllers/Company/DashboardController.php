<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Company;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/company/dashboard",
     *     summary="Get company dashboard",
     *     description="Get comprehensive dashboard data including company information, statistics, and recent activity",
     *     tags={"Company"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dashboard data retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="plan_id", type="integer", example=2),
     *                     @OA\Property(property="plan", type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Basic"),
     *                         @OA\Property(property="price", type="number", format="float", example=10.00),
     *                         @OA\Property(property="employee_limit", type="integer", example=50)
     *                     )
     *                 ),
     *                 @OA\Property(property="statistics", type="object",
     *                     @OA\Property(property="total_campaigns", type="integer", example=5),
     *                     @OA\Property(property="active_campaigns", type="integer", example=2),
     *                     @OA\Property(property="completed_campaigns", type="integer", example=3),
     *                     @OA\Property(property="total_targets", type="integer", example=150),
     *                     @OA\Property(property="total_interactions", type="integer", example=450),
     *                     @OA\Property(property="successful_simulations", type="integer", example=4),
     *                     @OA\Property(property="vulnerable_employees", type="integer", example=12)
     *                 ),
     *                 @OA\Property(property="recent_activity", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="type", type="string", example="campaign_completed"),
     *                         @OA\Property(property="message", type="string", example="Phishing Campaign #3 completed"),
     *                         @OA\Property(property="timestamp", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function dashboard(Request $request)
    {
        $company = $request->user();
        
        // Load relationships to avoid N+1 queries
        $company->load(['plan']);
        
        // Get campaign IDs for this company (more efficient)
        $campaignIds = $company->campaigns()->pluck('id');
        
        // Get campaign statistics efficiently
        $campaignsCount = $campaignIds->count();
        $activeCampaignsCount = $company->campaigns()
            ->whereIn('status', ['active', 'running'])
            ->count();
        
        // Get user count
        $usersCount = $company->users()->count();
        
        // Get total interactions across all campaigns (optimized query)
        $interactionsCount = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->count();
        
        // Get total targets across all campaigns
        $totalTargets = \App\Models\CampaignTarget::whereIn('campaign_id', $campaignIds)->count();
        
        // Get recent campaigns (last 5) with relationships
        $recentCampaigns = $company->campaigns()
            ->with(['targets', 'interactions'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Add computed attributes to the company model
        $company->campaigns_count = $campaignsCount;
        $company->active_campaigns_count = $activeCampaignsCount;
        $company->users_count = $usersCount;
        $company->interactions_count = $interactionsCount;
        $company->total_targets = $totalTargets;
        $company->recent_campaigns = $recentCampaigns;
        
        return new DashboardResource($company);
    }

    /**
     * Get detailed dashboard statistics
     */
    public function stats(Request $request)
    {
        $company = $request->user();
        $campaignIds = $company->campaigns()->pluck('id');
        
        $stats = [
            'campaigns' => [
                'total' => $company->campaigns()->count(),
                'active' => $company->campaigns()->whereIn('status', ['active', 'running'])->count(),
                'completed' => $company->campaigns()->where('status', 'completed')->count(),
                'draft' => $company->campaigns()->where('status', 'draft')->count(),
                'paused' => $company->campaigns()->where('status', 'paused')->count(),
            ],
            'targets' => [
                'total' => \App\Models\CampaignTarget::whereIn('campaign_id', $campaignIds)->count(),
                'unique' => \App\Models\CampaignTarget::whereIn('campaign_id', $campaignIds)->distinct('email')->count(),
            ],
            'interactions' => [
                'total' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->count(),
                'sent' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'sent')->count(),
                'opened' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'opened')->count(),
                'clicked' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'clicked')->count(),
                'submitted' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'submitted')->count(),
            ],
            'users' => [
                'total' => $company->users()->count(),
                'active' => $company->users()->where('is_active', true)->count(),
            ],
        ];

        return response()->json([
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => $stats
        ]);
    }

    /**
     * Get dashboard analytics
     */
    public function analytics(Request $request)
    {
        $company = $request->user();
        $campaignIds = $company->campaigns()->pluck('id');
        
        $totalSent = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
            ->where('action_type', 'sent')->count();
        $totalOpened = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
            ->where('action_type', 'opened')->count();
        $totalClicked = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
            ->where('action_type', 'clicked')->count();
        $totalSubmitted = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
            ->where('action_type', 'submitted')->count();

        $analytics = [
            'rates' => [
                'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
                'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
                'submit_rate' => $totalSent > 0 ? round(($totalSubmitted / $totalSent) * 100, 2) : 0,
            ],
            'trends' => [
                'campaigns_this_month' => $company->campaigns()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'campaigns_last_month' => $company->campaigns()
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->count(),
            ],
            'vulnerability' => [
                'high_risk' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
                    ->where('action_type', 'submitted')->distinct('email')->count(),
                'medium_risk' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
                    ->where('action_type', 'clicked')->distinct('email')->count(),
                'low_risk' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
                    ->where('action_type', 'opened')->distinct('email')->count(),
            ],
        ];

        return response()->json([
            'message' => 'Dashboard analytics retrieved successfully',
            'data' => $analytics
        ]);
    }

    /**
     * Get recent activity
     */
    public function recentActivity(Request $request)
    {
        $company = $request->user();
        $limit = $request->get('limit', 10);
        
        $activities = [];
        
        // Recent campaigns
        $recentCampaigns = $company->campaigns()
            ->latest()
            ->limit($limit)
            ->get();
        
        foreach ($recentCampaigns as $campaign) {
            $activities[] = [
                'type' => 'campaign_' . $campaign->status,
                'title' => ucfirst($campaign->type) . ' Campaign #' . $campaign->id,
                'message' => 'Campaign ' . $campaign->status,
                'timestamp' => $campaign->updated_at,
                'icon' => '📧',
            ];
        }
        
        // Recent interactions
        $campaignIds = $company->campaigns()->pluck('id');
        $recentInteractions = \App\Models\Interaction::whereIn('campaign_id', $campaignIds)
            ->latest('timestamp')
            ->limit($limit)
            ->get();
        
        foreach ($recentInteractions as $interaction) {
            $activities[] = [
                'type' => 'interaction_' . $interaction->action_type,
                'title' => ucfirst($interaction->action_type) . ' - ' . $interaction->email,
                'message' => 'Email ' . $interaction->action_type,
                'timestamp' => $interaction->timestamp,
                'icon' => $this->getInteractionIcon($interaction->action_type),
            ];
        }
        
        // Sort by timestamp and limit
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        $activities = array_slice($activities, 0, $limit);

        return response()->json([
            'message' => 'Recent activity retrieved successfully',
            'data' => $activities
        ]);
    }

    /**
     * Get performance metrics
     */
    public function performance(Request $request)
    {
        $company = $request->user();
        $campaignIds = $company->campaigns()->pluck('id');
        
        $campaigns = $company->campaigns()
            ->withCount(['targets', 'interactions'])
            ->get();
        
        $performance = [];
        foreach ($campaigns as $campaign) {
            $sent = $campaign->interactions->where('action_type', 'sent')->count();
            $opened = $campaign->interactions->where('action_type', 'opened')->count();
            $clicked = $campaign->interactions->where('action_type', 'clicked')->count();
            $submitted = $campaign->interactions->where('action_type', 'submitted')->count();
            
            $performance[] = [
                'campaign_id' => $campaign->id,
                'type' => $campaign->type,
                'status' => $campaign->status,
                'targets_count' => $campaign->targets_count,
                'open_rate' => $sent > 0 ? round(($opened / $sent) * 100, 2) : 0,
                'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 2) : 0,
                'submit_rate' => $sent > 0 ? round(($submitted / $sent) * 100, 2) : 0,
                'created_at' => $campaign->created_at,
            ];
        }

        return response()->json([
            'message' => 'Performance metrics retrieved successfully',
            'data' => $performance
        ]);
    }

    /**
     * Get charts data
     */
    public function charts(Request $request)
    {
        $company = $request->user();
        $campaignIds = $company->campaigns()->pluck('id');
        
        // Campaigns over time
        $campaignsOverTime = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = $company->campaigns()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $campaignsOverTime[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        
        // Interactions by type
        $interactionsByType = [
            'sent' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'sent')->count(),
            'opened' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'opened')->count(),
            'clicked' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'clicked')->count(),
            'submitted' => \App\Models\Interaction::whereIn('campaign_id', $campaignIds)->where('action_type', 'submitted')->count(),
        ];
        
        // Campaign status distribution
        $statusDistribution = [
            'active' => $company->campaigns()->whereIn('status', ['active', 'running'])->count(),
            'completed' => $company->campaigns()->where('status', 'completed')->count(),
            'draft' => $company->campaigns()->where('status', 'draft')->count(),
            'paused' => $company->campaigns()->where('status', 'paused')->count(),
        ];

        return response()->json([
            'message' => 'Charts data retrieved successfully',
            'data' => [
                'campaigns_over_time' => $campaignsOverTime,
                'interactions_by_type' => $interactionsByType,
                'status_distribution' => $statusDistribution,
            ]
        ]);
    }

    /**
     * Get icon for interaction type
     */
    private function getInteractionIcon($actionType)
    {
        return match($actionType) {
            'sent' => '📧',
            'opened' => '👁️',
            'clicked' => '🖱️',
            'submitted' => '⚠️',
            'failed' => '❌',
            default => '📊',
        };
    }
}
