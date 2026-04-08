<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\AddTargetsRequest;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\EmailTemplateResource;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\EmailTemplate;
use App\Services\EmailService;
use App\Services\AIAnalysisService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CampaignController extends Controller
{
    protected $emailService;
    protected $aiAnalysisService;

    public function __construct(EmailService $emailService, AIAnalysisService $aiAnalysisService)
    {
        $this->emailService = $emailService;
        $this->aiAnalysisService = $aiAnalysisService;
    }
    /**
     * @OA\Get(
     *     path="/api/campaign/templates",
     *     summary="List available phishing templates",
     *     description="Get all available email templates for phishing campaigns",
     *     tags={"Campaign"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Templates retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Templates retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Password Reset"),
     *                     @OA\Property(property="type", type="string", example="phishing"),
     *                     @OA\Property(property="subject", type="string", example="Urgent: Password Reset Required"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
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
    public function templates(Request $request)
    {
        $templates = EmailTemplate::all();
        
        return response()->json([
            'message' => 'Email templates retrieved successfully',
            'data' => EmailTemplateResource::collection($templates)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/campaign/create",
     *     summary="Create a new campaign",
     *     description="Create a new phishing simulation campaign with specified type and date range",
     *     tags={"Campaign"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","start_date","end_date"},
     *             @OA\Property(property="type", type="string", enum={"phishing","awareness","training"}, example="phishing"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-09-06"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-09-13")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Campaign created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaign created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", example="phishing"),
     *                 @OA\Property(property="status", type="string", example="draft"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-09-06"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-09-13"),
     *                 @OA\Property(property="targets_count", type="integer", example=0),
     *                 @OA\Property(property="interactions_count", type="integer", example=0)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function create(CreateCampaignRequest $request)
    {
        $company = $request->user();
        
        $campaign = Campaign::create([
            'company_id' => $company->id,
            'type' => $request->type,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json([
            'message' => 'Campaign created successfully',
            'data' => new CampaignResource($campaign)
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/campaign/add-targets",
     *     summary="Add targets to campaign",
     *     description="Add employee targets to an existing campaign for phishing simulation",
     *     tags={"Campaign"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"campaign_id","targets"},
     *             @OA\Property(property="campaign_id", type="integer", example=1, description="ID of the campaign"),
     *             @OA\Property(property="targets", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", format="email", example="john.doe@company.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Targets added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Targets added successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="campaign", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="phishing"),
     *                     @OA\Property(property="status", type="string", example="draft"),
     *                     @OA\Property(property="targets_count", type="integer", example=3)
     *                 ),
     *                 @OA\Property(property="targets_added", type="integer", example=3)
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
     *         description="Campaign not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="targets.0.email", type="array", @OA\Items(type="string", example="The targets.0.email field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function addTargets(AddTargetsRequest $request, string $campaignId = null)
    {
        $company = $request->user();
        $company->load('plan');
        
        // If campaignId is not provided in the route, get it from the request
        if (!$campaignId) {
            $campaignId = $request->input('campaign_id');
        }
        
        $campaign = Campaign::findOrFail($campaignId);
        
        $this->authorize('addTargets', $campaign);

        // Check employee limit from plan
        $plan = $company->plan;
        $employeeLimit = $plan->employee_limit;
        
        // Calculate current total targets across all campaigns for this company
        $currentTotalTargets = \App\Models\CampaignTarget::whereHas('campaign', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->count();
        
        // Count new targets being added
        $newTargetsCount = count($request->targets);
        $totalAfterAdding = $currentTotalTargets + $newTargetsCount;
        
        // Check if limit is unlimited (-1 means unlimited)
        if ($employeeLimit != -1 && $totalAfterAdding > $employeeLimit) {
            $remainingSlots = max(0, $employeeLimit - $currentTotalTargets);
            
            return response()->json([
                'message' => "You have reached the limit for your plan ({$plan->name}). Your plan allows {$employeeLimit} employees/targets. You currently have {$currentTotalTargets} targets. " . 
                    ($remainingSlots > 0 ? "You can add up to {$remainingSlots} more target(s)." : "You cannot add more targets. Please upgrade your plan to add more targets."),
                'error' => 'plan_limit_exceeded',
                'current_targets' => $currentTotalTargets,
                'limit' => $employeeLimit,
                'remaining_slots' => $remainingSlots,
            ], 403);
        }

        $targets = [];
        foreach ($request->targets as $targetData) {
            $target = CampaignTarget::create([
                'campaign_id' => $campaign->id,
                'name' => $targetData['name'],
                'email' => $targetData['email'],
            ]);
            $targets[] = $target;
        }

        return response()->json([
            'message' => 'Targets added successfully',
            'data' => [
                'campaign' => new CampaignResource($campaign->load('targets')),
                'targets_added' => count($targets)
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/campaign/{id}/details",
     *     summary="Get campaign details",
     *     description="Get detailed information about a specific campaign including targets and interactions",
     *     tags={"Campaign"},
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
     *         description="Campaign details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaign details retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", example="phishing"),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2025-09-06"),
     *                 @OA\Property(property="end_date", type="string", format="date", example="2025-09-13"),
     *                 @OA\Property(property="targets_count", type="integer", example=3),
     *                 @OA\Property(property="interactions_count", type="integer", example=9),
     *                 @OA\Property(property="targets", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                         @OA\Property(property="interactions", type="array", @OA\Items(type="object"))
     *                     )
     *                 ),
     *                 @OA\Property(property="interactions", type="array", @OA\Items(type="object"))
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
     *         description="Campaign not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function details(Request $request, string $campaignId)
    {
        $company = $request->user();
        $campaign = Campaign::with(['targets', 'interactions'])
            ->withCount(['targets', 'interactions'])
            ->findOrFail($campaignId);
        
        $this->authorize('view', $campaign);

        return response()->json([
            'message' => 'Campaign details retrieved successfully',
            'data' => new CampaignResource($campaign)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/campaigns",
     *     summary="List company campaigns",
     *     description="Get a paginated list of all campaigns for the authenticated company",
     *     tags={"Campaign"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of campaigns per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter campaigns by status",
     *         @OA\Schema(type="string", enum={"draft","active","completed","paused"}, example="active")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter campaigns by type",
     *         @OA\Schema(type="string", enum={"phishing","awareness","training"}, example="phishing")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Campaigns retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaigns retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="company_id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="phishing"),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="start_date", type="string", format="date", example="2025-09-06"),
     *                     @OA\Property(property="end_date", type="string", format="date", example="2025-09-13"),
     *                     @OA\Property(property="targets_count", type="integer", example=10),
     *                     @OA\Property(property="interactions_count", type="integer", example=25),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=45)
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
        $company = $request->user();
        
        $campaigns = Campaign::where('company_id', $company->id)
            ->with(['targets', 'interactions'])
            ->withCount(['targets', 'interactions'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Campaigns retrieved successfully',
            'data' => CampaignResource::collection($campaigns)
        ]);
    }

    /**
     * Store a newly created campaign
     */
    public function store(Request $request)
    {
        // This method is kept for backward compatibility
        // Use the create method instead
        return $this->create($request);
    }

    /**
     * Display the specified campaign
     */
    public function show(string $id)
    {
        // This method is kept for backward compatibility
        // Use the details method instead
        return $this->details(request(), $id);
    }

    /**
     * Update the specified campaign
     */
    public function update(Request $request, string $id)
    {
        $company = $request->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $campaign->update($request->only(['type', 'start_date', 'end_date']));

        return response()->json([
            'message' => 'Campaign updated successfully',
            'data' => new CampaignResource($campaign)
        ]);
    }

    /**
     * Remove the specified campaign
     */
    public function destroy(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $campaign->delete();

        return response()->json([
            'message' => 'Campaign deleted successfully'
        ]);
    }

    /**
     * Launch a campaign
     */
    public function launch(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        if ($campaign->status !== 'draft') {
            return response()->json([
                'message' => 'Only draft campaigns can be launched'
            ], 400);
        }

        $campaign->update(['status' => 'active']);

        return response()->json([
            'message' => 'Campaign launched successfully',
            'data' => new CampaignResource($campaign)
        ]);
    }

    /**
     * Pause a campaign
     */
    public function pause(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        if ($campaign->status !== 'active') {
            return response()->json([
                'message' => 'Only active campaigns can be paused'
            ], 400);
        }

        $campaign->update(['status' => 'paused']);

        return response()->json([
            'message' => 'Campaign paused successfully',
            'data' => new CampaignResource($campaign)
        ]);
    }

    /**
     * Stop a campaign
     */
    public function stop(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $campaign->update(['status' => 'stopped']);

        return response()->json([
            'message' => 'Campaign stopped successfully',
            'data' => new CampaignResource($campaign)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/campaign/{id}/send-emails",
     *     summary="Send campaign emails",
     *     description="Queue and send phishing simulation emails to all campaign targets",
     *     tags={"Campaign"},
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
     *         description="Emails queued for sending successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Emails queued for sending successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="campaign_id", type="integer", example=1),
     *                 @OA\Property(property="emails_queued", type="integer", example=3),
     *                 @OA\Property(property="estimated_delivery", type="string", format="date-time"),
     *                 @OA\Property(property="status", type="string", example="queued")
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
    public function sendEmails(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->with('targets')
            ->firstOrFail();

        if ($campaign->status !== 'active') {
            return response()->json([
                'message' => 'Only active campaigns can send emails'
            ], 400);
        }

        if ($campaign->targets->isEmpty()) {
            return response()->json([
                'message' => 'No targets found for this campaign'
            ], 400);
        }

        try {
            $results = $this->emailService->sendCampaignEmails($campaign);
            
            return response()->json([
                'message' => 'Campaign emails queued successfully',
                'data' => [
                    'campaign' => new CampaignResource($campaign),
                    'results' => $results
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send campaign emails',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/campaign/{id}/stats",
     *     summary="Get campaign statistics",
     *     description="Get comprehensive statistics and performance metrics for a specific campaign",
     *     tags={"Campaign"},
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
     *         description="Campaign statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaign statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="campaign", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="type", type="string", example="phishing"),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 ),
     *                 @OA\Property(property="stats", type="object",
     *                     @OA\Property(property="total_targets", type="integer", example=10),
     *                     @OA\Property(property="total_sent", type="integer", example=10),
     *                     @OA\Property(property="total_opened", type="integer", example=8),
     *                     @OA\Property(property="total_clicked", type="integer", example=3),
     *                     @OA\Property(property="total_submitted", type="integer", example=1),
     *                     @OA\Property(property="total_failed", type="integer", example=0),
     *                     @OA\Property(property="open_rate", type="number", format="float", example=80.0),
     *                     @OA\Property(property="click_rate", type="number", format="float", example=30.0),
     *                     @OA\Property(property="submit_rate", type="number", format="float", example=10.0),
     *                     @OA\Property(property="vulnerability_rate", type="number", format="float", example=10.0),
     *                     @OA\Property(property="vulnerable_employees", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="name", type="string", example="John Doe"),
     *                             @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                             @OA\Property(property="risk_level", type="string", example="high"),
     *                             @OA\Property(property="actions", type="array", @OA\Items(type="string"))
     *                         )
     *                     )
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
     *         description="Campaign not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function stats(string $id)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $id)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $stats = $this->emailService->getCampaignStats($campaign);

        return response()->json([
            'message' => 'Campaign statistics retrieved successfully',
            'data' => [
                'campaign' => new CampaignResource($campaign),
                'stats' => $stats
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/campaign/{campaignId}/resend-email/{targetId}",
     *     summary="Resend email to target",
     *     description="Resend phishing simulation email to a specific target in a campaign",
     *     tags={"Campaign"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="campaignId",
     *         in="path",
     *         required=true,
     *         description="Campaign ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="targetId",
     *         in="path",
     *         required=true,
     *         description="Target ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email resent successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="target_id", type="integer", example=1),
     *                 @OA\Property(property="campaign_id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", example="john.doe@company.com"),
     *                 @OA\Property(property="status", type="string", example="queued"),
     *                 @OA\Property(property="estimated_delivery", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Email resend failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to resend email")
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
     *         description="Campaign or target not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function resendEmail(string $campaignId, string $targetId)
    {
        $company = request()->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $target = CampaignTarget::where('id', $targetId)
            ->where('campaign_id', $campaignId)
            ->firstOrFail();

        $result = $this->emailService->resendEmail($campaign, $target);

        if ($result['success']) {
            return response()->json([
                'message' => $result['message']
            ]);
        } else {
            return response()->json([
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/campaign/{id}/ai-analysis",
     *     summary="Get AI analysis for campaign",
     *     description="Get comprehensive AI-powered analysis of campaign performance, training suggestions, and improvement recommendations",
     *     tags={"Campaign"},
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
     *         description="AI analysis completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="AI analysis completed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="campaign_id", type="integer", example=1),
     *                 @OA\Property(property="campaign_type", type="string", example="phishing"),
     *                 @OA\Property(property="analysis_date", type="string", format="date-time"),
     *                 @OA\Property(property="current_performance", type="object",
     *                     @OA\Property(property="total_targets", type="integer", example=10),
     *                     @OA\Property(property="open_rate", type="number", format="float", example=85.5),
     *                     @OA\Property(property="click_rate", type="number", format="float", example=25.0),
     *                     @OA\Property(property="submit_rate", type="number", format="float", example=5.0)
     *                 ),
     *                 @OA\Property(property="suggestions", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="type", type="string", example="critical"),
     *                         @OA\Property(property="title", type="string", example="Immediate Security Training Required"),
     *                         @OA\Property(property="description", type="string", example="1 employee(s) submitted credentials"),
     *                         @OA\Property(property="priority", type="string", example="high"),
     *                         @OA\Property(property="action_required", type="boolean", example=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="improvement", type="string", example="10% better than last campaign"),
     *                 @OA\Property(property="risk_level", type="object",
     *                     @OA\Property(property="level", type="string", example="medium"),
     *                     @OA\Property(property="description", type="string", example="Some employees are vulnerable")
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
    public function aiAnalysis(string $id)
    {
        $company = request()->user();
        $campaign = Campaign::findOrFail($id);
        
        $this->authorize('viewAiAnalysis', $campaign);

        try {
            $analysis = $this->aiAnalysisService->analyzeCampaign($campaign->id);
            
            if (isset($analysis['error'])) {
                return response()->json([
                    'message' => 'AI analysis failed',
                    'error' => $analysis['message']
                ], 500);
            }

            return response()->json([
                'message' => 'AI analysis completed successfully',
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate AI analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
