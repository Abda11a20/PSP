<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailTracking\SubmitFormRequest;
use App\Services\EmailService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EmailTrackingController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * @OA\Get(
     *     path="/api/track/{token}/opened",
     *     summary="Track email open",
     *     description="Track when an email is opened by returning a 1x1 transparent pixel. This endpoint is called automatically by email clients when images are loaded.",
     *     tags={"Email Tracking"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Unique campaign token",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tracking pixel returned successfully",
     *         @OA\MediaType(
     *             mediaType="image/gif",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content - Tracking failed",
     *         @OA\MediaType(
     *             mediaType="text/plain",
     *             @OA\Schema(type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function trackOpen(string $token)
    {
        $result = $this->emailService->trackInteraction($token, 'opened');
        
        if ($result['success']) {
            // Return a 1x1 transparent pixel
            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            
            return response($pixel, 200)
                ->header('Content-Type', 'image/gif')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }
        
        // Return empty response if tracking fails
        return response('', 204);
    }

    /**
     * @OA\Get(
     *     path="/api/track/{token}/clicked",
     *     summary="Track email link click",
     *     description="Track when a user clicks on a link in a phishing simulation email and redirect to the phishing page",
     *     tags={"Email Tracking"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Unique campaign token",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to phishing page",
     *         @OA\Header(
     *             header="Location",
     *             description="URL to redirect to",
     *             @OA\Schema(type="string", example="/api/campaign/abc123def456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campaign not found - Redirect to 404 page",
     *         @OA\Header(
     *             header="Location",
     *             description="URL to redirect to",
     *             @OA\Schema(type="string", example="https://example.com/404")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function trackClick(string $token)
    {
        $result = $this->emailService->trackInteraction($token, 'clicked');
        
        if ($result['success']) {
            // Redirect to the phishing page
            return redirect()->route('phishing.page', ['token' => $token]);
        }
        
        // Redirect to a generic page if tracking fails
        return redirect()->away(config('app.url') . '/404');
    }

    /**
     * @OA\Get(
     *     path="/api/campaign/{token}",
     *     summary="Show phishing simulation page",
     *     description="Display a phishing simulation page based on the campaign template. This page is shown to employees who click on phishing email links.",
     *     tags={"Email Tracking"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Unique campaign token",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Phishing page displayed successfully",
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(
     *                 type="string",
     *                 description="HTML content of the phishing simulation page",
     *                 example="<!DOCTYPE html><html><head><title>Password Reset</title></head><body>...</body></html>"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campaign not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Campaign not found"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid token"),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function showPhishingPage(string $token)
    {
        \Log::info('🔗 [Phishing Page] Access attempt', [
            'token' => $token,
            'url' => request()->fullUrl()
        ]);

        try {
            // Parse token to get campaign info
            $tokenData = $this->parseToken($token);
            
            \Log::info('🔗 [Phishing Page] Token parsed', [
                'token' => $token,
                'token_data' => $tokenData
            ]);
            
            if (!$tokenData) {
                \Log::warning('🔗 [Phishing Page] Invalid token format', [
                    'token' => $token,
                    'token_parts' => explode('_', $token)
                ]);
                return redirect()->away(config('app.url') . '/404');
            }

            // Get campaign and template
            $campaign = \App\Models\Campaign::with('targets')->find($tokenData['campaign_id']);
            
            \Log::info('🔗 [Phishing Page] Campaign lookup', [
                'token' => $token,
                'campaign_id' => $tokenData['campaign_id'],
                'campaign_found' => $campaign ? true : false
            ]);
            
            if (!$campaign) {
                \Log::warning('🔗 [Phishing Page] Campaign not found', [
                    'token' => $token,
                    'campaign_id' => $tokenData['campaign_id']
                ]);
                return redirect()->away(config('app.url') . '/404');
            }

            // Get the target
            $target = $campaign->targets->where('id', $tokenData['target_id'])->first();
            
            \Log::info('🔗 [Phishing Page] Target lookup', [
                'token' => $token,
                'target_id' => $tokenData['target_id'],
                'target_found' => $target ? true : false,
                'targets_count' => $campaign->targets->count()
            ]);
            
            if (!$target) {
                \Log::warning('🔗 [Phishing Page] Target not found', [
                    'token' => $token,
                    'target_id' => $tokenData['target_id'],
                    'campaign_id' => $tokenData['campaign_id']
                ]);
                return redirect()->away(config('app.url') . '/404');
            }

            // Get email template
            $template = \App\Models\EmailTemplate::where('type', $campaign->type)->first();
            
            \Log::info('🔗 [Phishing Page] Template lookup', [
                'token' => $token,
                'campaign_type' => $campaign->type,
                'template_found' => $template ? true : false
            ]);
            
            if (!$template) {
                \Log::warning('🔗 [Phishing Page] Template not found', [
                    'token' => $token,
                    'campaign_type' => $campaign->type
                ]);
                return redirect()->away(config('app.url') . '/404');
            }

            // Track the page view as 'clicked' if not already tracked
            $this->emailService->trackInteraction($token, 'clicked');

            \Log::info('🔗 [Phishing Page] Rendering page', [
                'token' => $token,
                'campaign_id' => $campaign->id,
                'target_email' => $target->email
            ]);

            // Return the phishing page view
            return view('phishing-page', [
                'token' => $token,
                'campaign' => $campaign,
                'target' => $target,
                'template' => $template,
                'campaignId' => $tokenData['campaign_id'],
                'email' => $target->email
            ]);

        } catch (\Exception $e) {
            \Log::error('🔗 [Phishing Page] Exception occurred', [
                'token' => $token,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->away(config('app.url') . '/404');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/campaign/{token}/submit",
     *     summary="Track phishing form submission",
     *     description="Track when an employee submits credentials to a phishing simulation (no real credentials are stored)",
     *     tags={"Email Tracking"},
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Unique campaign token",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","timestamp"},
     *             @OA\Property(property="email", type="string", format="email", example="employee@company.com"),
     *             @OA\Property(property="password", type="string", example="[REDACTED - NOT STORED]"),
     *             @OA\Property(property="department", type="string", example="IT"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="campaign_type", type="string", example="phishing"),
     *             @OA\Property(property="template_name", type="string", example="Password Reset")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Submission tracked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Submission tracked successfully"),
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="note", type="string", example="No real credentials were stored")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token or validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Campaign or target not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function trackSubmit(SubmitFormRequest $request, string $token)
    {
        try {
            // Parse token to get campaign info
            $tokenData = $this->parseToken($token);
            
            if (!$tokenData) {
                return response()->json([
                    'message' => 'Invalid token',
                    'success' => false
                ], 400);
            }

            // Get campaign and target info
            $campaign = \App\Models\Campaign::with('targets')->find($tokenData['campaign_id']);
            $target = $campaign ? $campaign->targets->where('id', $tokenData['target_id'])->first() : null;
            
            if (!$campaign || !$target) {
                return response()->json([
                    'message' => 'Campaign or target not found',
                    'success' => false
                ], 404);
            }

            // Prepare metadata (store password for simulation analysis)
            // Note: This is for phishing simulation purposes only
            $metadata = [];
            
            if ($request->filled('department')) {
                $metadata['department'] = $request->input('department');
            }
            
            $metadata['ip_address'] = $request->ip() ?: 'Unknown';
            $metadata['user_agent'] = $request->userAgent() ?: 'Unknown';
            
            if ($request->filled('campaign_type')) {
                $metadata['campaign_type'] = $request->input('campaign_type');
            } else {
                $metadata['campaign_type'] = $campaign->type;
            }
            
            if ($request->filled('template_name')) {
                $metadata['template_name'] = $request->input('template_name');
            } elseif ($campaign->emailTemplate) {
                $metadata['template_name'] = $campaign->emailTemplate->name;
            }
            
            // Store password for simulation analysis (this is a phishing simulation, not real credentials)
            if ($request->filled('password') && $request->input('password') !== '[REDACTED - NOT STORED]') {
                $metadata['password'] = $request->input('password');
                $metadata['password_provided'] = true;
            } else {
                $metadata['password_provided'] = false;
            }
            
            $metadata['note'] = 'This is a phishing simulation for security awareness training';
            
            // Track the submission (do NOT store real credentials)
            $result = $this->emailService->trackInteraction($token, 'submitted', $metadata);
            
            if ($result['success']) {
                // Log the submission attempt (without storing credentials)
                \Log::info('Phishing form submission tracked', [
                    'token' => $token,
                    'campaign_id' => $tokenData['campaign_id'],
                    'target_email' => $target->email,
                    'target_name' => $target->name,
                    'timestamp' => now(),
                    'metadata' => $metadata,
                    'note' => 'No real credentials were stored'
                ]);

                return response()->json([
                    'message' => 'Thank you for your submission. This was a phishing simulation.',
                    'success' => true,
                    'data' => [
                        'campaign_type' => $campaign->type,
                        'simulation_note' => 'No real credentials were collected or stored'
                    ]
                ]);
            }
            
            return response()->json([
                'message' => 'Failed to track submission',
                'success' => false
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Failed to track form submission', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Submission tracking failed',
                'success' => false
            ], 500);
        }
    }

    /**
     * Generate fake phishing page URL
     */
    protected function generateFakePhishingPage(string $token): string
    {
        $baseUrl = config('app.url');
        
        // In a real implementation, you would have different fake pages
        // based on the campaign type or template
        return "{$baseUrl}/fake-phishing-page?token={$token}";
    }

    /**
     * @OA\Get(
     *     path="/api/fake-phishing-page",
     *     summary="Show fake phishing page",
     *     description="Display a generic fake phishing page for testing or demonstration purposes. This endpoint is used when a valid campaign token is not available.",
     *     tags={"Email Tracking"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="Optional campaign token",
     *         @OA\Schema(type="string", example="abc123def456")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fake phishing page displayed successfully",
     *         @OA\MediaType(
     *             mediaType="text/html",
     *             @OA\Schema(
     *                 type="string",
     *                 description="HTML content of the fake phishing page",
     *                 example="<!DOCTYPE html><html><head><title>Fake Login</title></head><body>...</body></html>"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found - Redirect to 404",
     *         @OA\Header(
     *             header="Location",
     *             description="URL to redirect to",
     *             @OA\Schema(type="string", example="https://example.com/404")
     *         )
     *     )
     * )
     */
    public function showFakePhishingPage(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect()->away(config('app.url') . '/404');
        }

        // Parse token to get campaign info
        $tokenData = $this->parseToken($token);
        
        if (!$tokenData) {
            return redirect()->away(config('app.url') . '/404');
        }

        // Return a fake phishing page
        return view('fake-phishing-page', [
            'token' => $token,
            'campaignId' => $tokenData['campaign_id'],
            'email' => $tokenData['email']
        ]);
    }

    /**
     * Parse token to extract campaign information
     */
    protected function parseToken(string $token): ?array
    {
        try {
            $parts = explode('_', $token);
            
            \Log::info('🔗 [Parse Token] Token parts', [
                'token' => $token,
                'parts_count' => count($parts),
                'parts' => $parts
            ]);
            
            if (count($parts) < 4) {
                \Log::warning('🔗 [Parse Token] Invalid token format - not enough parts', [
                    'token' => $token,
                    'parts_count' => count($parts),
                    'expected' => 4
                ]);
                return null;
            }

            $tokenData = [
                'campaign_id' => (int) $parts[0],
                'target_id' => (int) $parts[1],
                'timestamp' => $parts[2],
                'random' => $parts[3]
            ];

            \Log::info('🔗 [Parse Token] Token parsed successfully', [
                'token' => $token,
                'token_data' => $tokenData
            ]);

            return $tokenData;

        } catch (\Exception $e) {
            \Log::error('🔗 [Parse Token] Exception', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
