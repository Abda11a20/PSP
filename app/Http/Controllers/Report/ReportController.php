<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\Interaction;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class ReportController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    /**
     * @OA\Get(
     *     path="/api/reports",
     *     summary="List available reports",
     *     description="Get a list of all available report types and recent reports for the authenticated company",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter reports by type",
     *         @OA\Schema(type="string", enum={"campaign","company","export"}, example="campaign")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter reports from date",
     *         @OA\Schema(type="string", format="date", example="2025-09-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter reports to date",
     *         @OA\Schema(type="string", format="date", example="2025-09-30")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reports list retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reports list retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="available_reports", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="type", type="string", example="campaign"),
     *                         @OA\Property(property="name", type="string", example="Campaign Analytics"),
     *                         @OA\Property(property="description", type="string", example="Detailed campaign performance metrics"),
     *                         @OA\Property(property="endpoint", type="string", example="/api/reports/campaign/{id}")
     *                     )
     *                 ),
     *                 @OA\Property(property="recent_reports", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="type", type="string", example="campaign"),
     *                         @OA\Property(property="name", type="string", example="Q3 Phishing Campaign"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="status", type="string", example="completed")
     *                     )
     *                 ),
     *                 @OA\Property(property="total_reports", type="integer", example=15)
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
    public function index(Request $request)
    {
        // TODO: Implement report listing logic
        return response()->json([
            'message' => 'Report listing endpoint',
            'data' => []
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/campaign/{id}",
     *     summary="Get comprehensive campaign report",
     *     description="Get detailed analytics and performance metrics for a specific campaign including interaction details, time analytics, and charts-ready data",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaign report retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaign report retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="campaign", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="phishing"),
     *                     @OA\Property(property="status", type="string", example="completed"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2025-09-06"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2025-09-13")
     *                 ),
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_targets", type="integer", example=10),
     *                     @OA\Property(property="open_rate", type="number", format="float", example=85.5),
     *                     @OA\Property(property="click_rate", type="number", format="float", example=25.0),
     *                     @OA\Property(property="submit_rate", type="number", format="float", example=5.0)
     *                 ),
     *                 @OA\Property(property="interaction_details", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="risk_level", type="string", example="high"),
     *                         @OA\Property(property="actions", type="array", @OA\Items(type="string"))
     *                     )
     *                 ),
     *                 @OA\Property(property="charts_data", type="object",
     *                     @OA\Property(property="interaction_timeline", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="department_breakdown", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="risk_level_distribution", type="array", @OA\Items(type="object"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campaign not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function campaignReport(string $campaignId)
    {
        try {
            $company = request()->user();
            
            // Get campaign with validation
            $campaign = Campaign::where('id', $campaignId)
                ->where('company_id', $company->id)
                ->with(['targets', 'interactions'])
                ->firstOrFail();

            // Get basic campaign statistics
            $totalTargets = $campaign->targets->count();
            $interactions = $campaign->interactions;
            
            // Calculate action counts
            $totalSent = $interactions->where('action_type', 'sent')->count();
            $totalOpened = $interactions->where('action_type', 'opened')->count();
            $totalClicked = $interactions->where('action_type', 'clicked')->count();
            $totalSubmitted = $interactions->where('action_type', 'submitted')->count();
            $totalFailed = $interactions->where('action_type', 'failed')->count();

            // Calculate percentages
            $openedPercentage = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;
            $clickedPercentage = $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0;
            $submittedPercentage = $totalSent > 0 ? round(($totalSubmitted / $totalSent) * 100, 2) : 0;

            // Get detailed interaction data with timestamps
            $interactionDetails = $this->getInteractionDetails($campaignId);
            
            // Get time-based analytics
            $timeAnalytics = $this->getTimeAnalytics($campaignId);
            
            // Get target-specific analytics
            $targetAnalytics = $this->getTargetAnalytics($campaignId);

            // Prepare charts-ready data
            $chartsData = $this->prepareChartsData($campaign, $interactions);

            $reportData = [
                'campaign' => [
                    'id' => $campaign->id,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                    'start_date' => $campaign->start_date,
                    'end_date' => $campaign->end_date,
                    'created_at' => $campaign->created_at,
                ],
                'summary' => [
                    'total_targets' => $totalTargets,
                    'total_sent' => $totalSent,
                    'total_opened' => $totalOpened,
                    'total_clicked' => $totalClicked,
                    'total_submitted' => $totalSubmitted,
                    'total_failed' => $totalFailed,
                    'opened_percentage' => $openedPercentage,
                    'clicked_percentage' => $clickedPercentage,
                    'submitted_percentage' => $submittedPercentage,
                ],
                'interaction_details' => $interactionDetails,
                'time_analytics' => $timeAnalytics,
                'target_analytics' => $targetAnalytics,
                'charts_data' => $chartsData,
            ];

            return response()->json([
                'message' => 'Campaign report retrieved successfully',
                'data' => $reportData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve campaign report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/companies/{company}",
     *     summary="Get company report",
     *     description="Get comprehensive analytics and performance metrics for a specific company including all campaigns, user activity, and security insights",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *         description="Company ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Report period",
     *         @OA\Schema(type="string", enum={"7d","30d","90d","1y","all"}, example="30d")
     *     ),
     *     @OA\Parameter(
     *         name="include_details",
     *         in="query",
     *         description="Include detailed breakdown",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company report retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Company report retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="plan", type="object",
     *                         @OA\Property(property="name", type="string", example="Premium"),
     *                         @OA\Property(property="employee_limit", type="integer", example=500)
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="summary", type="object",
     *                     @OA\Property(property="total_campaigns", type="integer", example=15),
     *                     @OA\Property(property="active_campaigns", type="integer", example=3),
     *                     @OA\Property(property="total_targets", type="integer", example=150),
     *                     @OA\Property(property="total_interactions", type="integer", example=450),
     *                     @OA\Property(property="average_open_rate", type="number", format="float", example=78.5),
     *                     @OA\Property(property="average_click_rate", type="number", format="float", example=22.3),
     *                     @OA\Property(property="average_submit_rate", type="number", format="float", example=8.7),
     *                     @OA\Property(property="security_score", type="number", format="float", example=85.2)
     *                 ),
     *                 @OA\Property(property="campaign_performance", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="campaign_id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Q3 Security Training"),
     *                         @OA\Property(property="type", type="string", example="phishing"),
     *                         @OA\Property(property="status", type="string", example="completed"),
     *                         @OA\Property(property="targets_count", type="integer", example=25),
     *                         @OA\Property(property="open_rate", type="number", format="float", example=80.0),
     *                         @OA\Property(property="click_rate", type="number", format="float", example=20.0),
     *                         @OA\Property(property="submit_rate", type="number", format="float", example=5.0),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(property="security_insights", type="object",
     *                     @OA\Property(property="vulnerable_employees", type="integer", example=12),
     *                     @OA\Property(property="high_risk_employees", type="integer", example=3),
     *                     @OA\Property(property="training_recommendations", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="improvement_areas", type="array", @OA\Items(type="string"))
     *                 ),
     *                 @OA\Property(property="charts_data", type="object",
     *                     @OA\Property(property="campaign_timeline", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="performance_trends", type="array", @OA\Items(type="object")),
     *                     @OA\Property(property="department_breakdown", type="array", @OA\Items(type="object"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Access denied",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function companyReport(string $companyId)
    {
        // TODO: Implement company report logic
        return response()->json([
            'message' => 'Company report',
            'data' => ['company_id' => $companyId]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/reports/export/{type}",
     *     summary="Export reports",
     *     description="Export reports in various formats (PDF, CSV, Excel) for campaigns, companies, or analytics data",
     *     tags={"Reports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Export type",
     *         @OA\Schema(type="string", enum={"campaign","company","analytics","interactions"}, example="campaign")
     *     ),
     *     @OA\Parameter(
     *         name="format",
     *         in="query",
     *         description="Export format",
     *         @OA\Schema(type="string", enum={"pdf","csv","xlsx","json"}, example="pdf")
     *     ),
     *     @OA\Parameter(
     *         name="campaign_id",
     *         in="query",
     *         description="Campaign ID (required for campaign exports)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         description="Company ID (required for company exports)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Start date for data export",
     *         @OA\Schema(type="string", format="date", example="2025-09-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="End date for data export",
     *         @OA\Schema(type="string", format="date", example="2025-09-30")
     *     ),
     *     @OA\Parameter(
     *         name="include_charts",
     *         in="query",
     *         description="Include charts in PDF exports",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report exported successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Report exported successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="export_id", type="string", example="exp_123456789"),
     *                 @OA\Property(property="type", type="string", example="campaign"),
     *                 @OA\Property(property="format", type="string", example="pdf"),
     *                 @OA\Property(property="file_url", type="string", example="https://api.example.com/exports/exp_123456789.pdf"),
     *                 @OA\Property(property="file_size", type="integer", example=2048576),
     *                 @OA\Property(property="expires_at", type="string", format="date-time"),
     *                 @OA\Property(property="download_count", type="integer", example=0),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid export parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid export parameters"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="campaign_id", type="array", @OA\Items(type="string", example="Campaign ID is required for campaign exports"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Access denied",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Export generation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Export generation failed"),
     *             @OA\Property(property="error", type="string", example="Unable to generate PDF file")
     *         )
     *     )
     * )
     */
    public function export(string $type)
    {
        // TODO: Implement report export logic
        return response()->json([
            'message' => 'Report exported successfully',
            'data' => ['type' => $type]
        ]);
    }

    /**
     * Get detailed interaction data with timestamps
     */
    protected function getInteractionDetails(string $campaignId): array
    {
        $interactions = Interaction::where('campaign_id', $campaignId)
            ->orderBy('timestamp', 'desc')
            ->get();

        $details = [];
        foreach ($interactions as $interaction) {
            $details[] = [
                'id' => $interaction->id,
                'email' => $interaction->email,
                'action_type' => $interaction->action_type,
                'timestamp' => $interaction->timestamp,
                'created_at' => $interaction->created_at,
                'time_ago' => $interaction->timestamp->diffForHumans(),
            ];
        }

        return $details;
    }

    /**
     * Get time-based analytics
     */
    protected function getTimeAnalytics(string $campaignId): array
    {
        // Get interactions grouped by hour
        $hourlyData = Interaction::where('campaign_id', $campaignId)
            ->selectRaw('DATE(timestamp) as date, HOUR(timestamp) as hour, action_type, COUNT(*) as count')
            ->groupBy('date', 'hour', 'action_type')
            ->orderBy('date')
            ->orderBy('hour')
            ->get();

        // Get interactions grouped by day
        $dailyData = Interaction::where('campaign_id', $campaignId)
            ->selectRaw('DATE(timestamp) as date, action_type, COUNT(*) as count')
            ->groupBy('date', 'action_type')
            ->orderBy('date')
            ->get();

        // Get response times (time between sent and opened/clicked/submitted)
        $responseTimes = $this->calculateResponseTimes($campaignId);

        return [
            'hourly' => $hourlyData,
            'daily' => $dailyData,
            'response_times' => $responseTimes,
        ];
    }

    /**
     * Get target-specific analytics
     */
    protected function getTargetAnalytics(string $campaignId): array
    {
        $targets = CampaignTarget::where('campaign_id', $campaignId)
            ->with('interactions')
            ->get();

        $analytics = [];
        foreach ($targets as $target) {
            $interactions = $target->interactions;
            $analytics[] = [
                'target_id' => $target->id,
                'name' => $target->name,
                'email' => $target->email,
                'actions' => [
                    'sent' => $interactions->where('action_type', 'sent')->count(),
                    'opened' => $interactions->where('action_type', 'opened')->count(),
                    'clicked' => $interactions->where('action_type', 'clicked')->count(),
                    'submitted' => $interactions->where('action_type', 'submitted')->count(),
                    'failed' => $interactions->where('action_type', 'failed')->count(),
                ],
                'first_action' => $interactions->min('timestamp'),
                'last_action' => $interactions->max('timestamp'),
                'total_actions' => $interactions->count(),
            ];
        }

        return $analytics;
    }

    /**
     * Calculate response times between actions
     */
    protected function calculateResponseTimes(string $campaignId): array
    {
        $interactions = Interaction::where('campaign_id', $campaignId)
            ->orderBy('email')
            ->orderBy('timestamp')
            ->get()
            ->groupBy('email');

        $responseTimes = [];
        foreach ($interactions as $email => $userInteractions) {
            $sentTime = $userInteractions->where('action_type', 'sent')->first()?->timestamp;
            
            if ($sentTime) {
                $openedTime = $userInteractions->where('action_type', 'opened')->first()?->timestamp;
                $clickedTime = $userInteractions->where('action_type', 'clicked')->first()?->timestamp;
                $submittedTime = $userInteractions->where('action_type', 'submitted')->first()?->timestamp;

                $responseTimes[$email] = [
                    'email' => $email,
                    'sent_at' => $sentTime,
                    'opened_at' => $openedTime,
                    'clicked_at' => $clickedTime,
                    'submitted_at' => $submittedTime,
                    'time_to_open' => $openedTime ? $sentTime->diffInMinutes($openedTime) : null,
                    'time_to_click' => $clickedTime ? $sentTime->diffInMinutes($clickedTime) : null,
                    'time_to_submit' => $submittedTime ? $sentTime->diffInMinutes($submittedTime) : null,
                ];
            }
        }

        return array_values($responseTimes);
    }

    /**
     * Prepare charts-ready data
     */
    protected function prepareChartsData(Campaign $campaign, $interactions): array
    {
        // Pie chart data for action distribution
        $actionDistribution = [
            ['label' => 'Opened', 'value' => $interactions->where('action_type', 'opened')->count()],
            ['label' => 'Clicked', 'value' => $interactions->where('action_type', 'clicked')->count()],
            ['label' => 'Submitted', 'value' => $interactions->where('action_type', 'submitted')->count()],
            ['label' => 'Failed', 'value' => $interactions->where('action_type', 'failed')->count()],
        ];

        // Line chart data for timeline
        $timelineData = [];
        $startDate = $campaign->start_date;
        $endDate = $campaign->end_date ?? now();
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayInteractions = $interactions->filter(function ($interaction) use ($dateStr) {
                return $interaction->timestamp->format('Y-m-d') === $dateStr;
            });

            $timelineData[] = [
                'date' => $dateStr,
                'opened' => $dayInteractions->where('action_type', 'opened')->count(),
                'clicked' => $dayInteractions->where('action_type', 'clicked')->count(),
                'submitted' => $dayInteractions->where('action_type', 'submitted')->count(),
            ];

            $currentDate->addDay();
        }

        // Bar chart data for hourly distribution
        $hourlyDistribution = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourInteractions = $interactions->filter(function ($interaction) use ($hour) {
                return $interaction->timestamp->hour === $hour;
            });

            $hourlyDistribution[] = [
                'hour' => $hour,
                'opened' => $hourInteractions->where('action_type', 'opened')->count(),
                'clicked' => $hourInteractions->where('action_type', 'clicked')->count(),
                'submitted' => $hourInteractions->where('action_type', 'submitted')->count(),
            ];
        }

        // Conversion funnel data
        $totalSent = $interactions->where('action_type', 'sent')->count();
        $totalOpened = $interactions->where('action_type', 'opened')->count();
        $totalClicked = $interactions->where('action_type', 'clicked')->count();
        $totalSubmitted = $interactions->where('action_type', 'submitted')->count();

        $conversionFunnel = [
            ['stage' => 'Sent', 'count' => $totalSent, 'percentage' => 100],
            ['stage' => 'Opened', 'count' => $totalOpened, 'percentage' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0],
            ['stage' => 'Clicked', 'count' => $totalClicked, 'percentage' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0],
            ['stage' => 'Submitted', 'count' => $totalSubmitted, 'percentage' => $totalSent > 0 ? round(($totalSubmitted / $totalSent) * 100, 2) : 0],
        ];

        return [
            'action_distribution' => $actionDistribution,
            'timeline' => $timelineData,
            'hourly_distribution' => $hourlyDistribution,
            'conversion_funnel' => $conversionFunnel,
        ];
    }
}
