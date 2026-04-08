<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\Company;
use App\Models\Plan;
use App\Services\EmailService;
use App\Services\AIAnalysisService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClientPageController extends Controller
{
    protected $emailService;
    protected $aiAnalysisService;
    protected $telegramService;

    public function __construct(EmailService $emailService, AIAnalysisService $aiAnalysisService, TelegramService $telegramService)
    {
        $this->emailService = $emailService;
        $this->aiAnalysisService = $aiAnalysisService;
        $this->telegramService = $telegramService;
    }

    public function createCampaign()
    {
        $company = Auth::guard('company')->user();
        $templates = \App\Models\EmailTemplate::orderBy('type')->orderBy('name')->get();
        return view('client.pages.create-campaign', compact('company', 'templates'));
    }

    public function viewTemplates()
    {
        $company = Auth::guard('company')->user();
        $templates = \App\Models\EmailTemplate::latest()->paginate(12);
        return view('client.pages.templates', compact('company', 'templates'));
    }

    public function previewTemplate($templateId)
    {
        $company = Auth::guard('company')->user();
        $template = \App\Models\EmailTemplate::findOrFail($templateId);
        
        // Replace placeholders with sample data for preview
        $previewContent = $template->html_content;
        
        // User information
        $previewContent = str_replace('{{name}}', 'John Doe', $previewContent);
        $previewContent = str_replace('{{email}}', 'john.doe@company.com', $previewContent);
        $previewContent = str_replace('{{employee_name}}', 'John Doe', $previewContent);
        
        // Link placeholders (all replaced with # for preview)
        $previewContent = str_replace('{{fake_link}}', '#', $previewContent);
        $previewContent = str_replace('{{reset_link}}', '#', $previewContent);
        $previewContent = str_replace('{{login_link}}', '#', $previewContent);
        $previewContent = str_replace('{{verify_link}}', '#', $previewContent);
        $previewContent = str_replace('{{verification_link}}', '#', $previewContent);
        $previewContent = str_replace('{{training_link}}', '#', $previewContent);
        $previewContent = str_replace('{{status_link}}', '#', $previewContent);
        
        // Date placeholders
        $previewContent = str_replace('{{deadline}}', now()->addDays(7)->format('F d, Y'), $previewContent);
        $previewContent = str_replace('{{maintenance_date}}', now()->addDays(3)->format('F d, Y'), $previewContent);
        
        // System placeholders
        $previewContent = str_replace('{{tracking_pixel}}', '<img src="#" width="1" height="1" style="display:none;" />', $previewContent);
        $previewContent = str_replace('{{campaign_name}}', 'phishing', $previewContent);
        
        return view('client.pages.template-preview', compact('company', 'template', 'previewContent'));
    }

    public function updateTemplate(Request $request, $templateId)
    {
        $company = Auth::guard('company')->user();
        $template = \App\Models\EmailTemplate::findOrFail($templateId);
        
        $request->validate([
            'html_content' => 'required|string',
        ]);
        
        $template->update([
            'html_content' => $request->html_content,
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully!',
                'template' => $template,
            ]);
        }
        
        return redirect()->route('client.templates.preview', $template->id)
            ->with('success', 'Template updated successfully!');
    }

    public function useTemplate($templateId)
    {
        $company = Auth::guard('company')->user();
        $template = \App\Models\EmailTemplate::findOrFail($templateId);
        
        // Redirect to create campaign with template type pre-selected
        return redirect()->route('client.campaigns.create', ['template_type' => $template->type])
            ->with('success', 'Template selected! Create a campaign using the "' . $template->name . '" template.');
    }

    // Phishing Pages Management
    public function viewPhishingPages()
    {
        $company = Auth::guard('company')->user();
        $phishingPages = \App\Models\PhishingPage::latest()->paginate(12);
        return view('client.pages.phishing-pages', compact('company', 'phishingPages'));
    }

    public function createPhishingPage()
    {
        $company = Auth::guard('company')->user();
        return view('client.pages.phishing-page-create', compact('company'));
    }

    public function storePhishingPage(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:phishing_pages,slug',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'type' => 'required|in:phishing,awareness,training',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?: \Illuminate\Support\Str::slug($request->name);
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (\App\Models\PhishingPage::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $phishingPage = \App\Models\PhishingPage::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'html_content' => $request->html_content,
            'type' => $request->type,
            'is_active' => $request->has('is_active') ? true : false,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        return redirect()->route('client.phishing-pages')
            ->with('success', 'Phishing page created successfully!');
    }

    public function editPhishingPage($phishingPageId)
    {
        $company = Auth::guard('company')->user();
        $phishingPage = \App\Models\PhishingPage::findOrFail($phishingPageId);
        return view('client.pages.phishing-page-edit', compact('company', 'phishingPage'));
    }

    public function updatePhishingPage(Request $request, $phishingPageId)
    {
        $company = Auth::guard('company')->user();
        $phishingPage = \App\Models\PhishingPage::findOrFail($phishingPageId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:phishing_pages,slug,' . $phishingPageId,
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'type' => 'required|in:phishing,awareness,training',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        // Generate slug if not provided
        $slug = $request->slug ?: \Illuminate\Support\Str::slug($request->name);
        // Ensure slug is unique (excluding current page)
        if ($slug !== $phishingPage->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (\App\Models\PhishingPage::where('slug', $slug)->where('id', '!=', $phishingPageId)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $phishingPage->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'html_content' => $request->html_content,
            'type' => $request->type,
            'is_active' => $request->has('is_active') ? true : false,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'seo_keywords' => $request->seo_keywords,
        ]);

        return redirect()->route('client.phishing-pages')
            ->with('success', 'Phishing page updated successfully!');
    }

    public function deletePhishingPage($phishingPageId)
    {
        $company = Auth::guard('company')->user();
        $phishingPage = \App\Models\PhishingPage::findOrFail($phishingPageId);
        $phishingPage->delete();

        return redirect()->route('client.phishing-pages')
            ->with('success', 'Phishing page deleted successfully!');
    }

    public function previewPhishingPage($phishingPageId)
    {
        $company = Auth::guard('company')->user();
        $phishingPage = \App\Models\PhishingPage::findOrFail($phishingPageId);
        
        // Replace placeholders with sample data for preview
        $previewContent = $phishingPage->html_content;
        $previewContent = str_replace('{{employee_name}}', '', $previewContent);
        $previewContent = str_replace('{{reset_link}}', '#', $previewContent);
        $previewContent = str_replace('{{training_link}}', '#', $previewContent);
        $previewContent = str_replace('{{deadline}}', now()->addDays(7)->format('F d, Y'), $previewContent);
        $previewContent = str_replace('{{maintenance_date}}', now()->addDays(3)->format('F d, Y'), $previewContent);
        $previewContent = str_replace('{{status_link}}', '#', $previewContent);
        
        return view('client.pages.phishing-page-preview', compact('company', 'phishingPage', 'previewContent'));
    }

    // Public view for phishing pages
    public function showPublicPhishingPage($slug)
    {
        $phishingPage = \App\Models\PhishingPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        // Replace placeholders with sample data
        $previewContent = $phishingPage->html_content;
        $previewContent = str_replace('{{employee_name}}', '', $previewContent);
        $previewContent = str_replace('{{reset_link}}', '#', $previewContent);
        $previewContent = str_replace('{{training_link}}', '#', $previewContent);
        $previewContent = str_replace('{{deadline}}', now()->addDays(7)->format('F d, Y'), $previewContent);
        $previewContent = str_replace('{{maintenance_date}}', now()->addDays(3)->format('F d, Y'), $previewContent);
        $previewContent = str_replace('{{status_link}}', '#', $previewContent);
        
        return view('public.phishing-page', compact('phishingPage', 'previewContent'));
    }

    public function manageUsers()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $users = $company->users()->latest()->paginate(15);
        return view('client.pages.users', compact('company', 'users'));
    }

    public function showInviteUser()
    {
        $company = Auth::guard('company')->user();
        return view('client.pages.invite-user', compact('company'));
    }

    public function inviteUser(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,manager,user',
        ]);

        // Check if user already exists
        $existingUser = \App\Models\User::where('email', $request->email)
            ->where('company_id', $company->id)
            ->first();

        if ($existingUser) {
            return redirect()->back()
                ->with('error', 'A user with this email already exists in your company.')
                ->withInput();
        }

        // Generate temporary password
        $temporaryPassword = \Illuminate\Support\Str::random(12);
        
        try {
            $user = \App\Models\User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($temporaryPassword),
                'role' => $request->role,
                'is_active' => true,
            ]);

            // Send invitation email
            try {
                Mail::send('emails.user-invitation', [
                    'user' => $user,
                    'company' => $company,
                    'temporaryPassword' => $temporaryPassword,
                    'registerUrl' => route('register'),
                ], function ($message) use ($user, $company) {
                    $message->to($user->email, $user->name)
                        ->subject('Invitation to join ' . $company->name . ' - Phishing Simulation Platform');
                });
            } catch (\Exception $mailException) {
                Log::error('Failed to send invitation email', [
                    'error' => $mailException->getMessage(),
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                // User is created, but email failed - still show success but with warning
                return redirect()->route('client.users')
                    ->with('warning', 'User created successfully, but invitation email failed to send. Please contact the user directly.');
            }

            return redirect()->route('client.users')
                ->with('success', 'Invitation sent successfully to ' . $user->email);
                
        } catch (\Exception $e) {
            Log::error('Failed to invite user', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'company_id' => $company->id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create user. Please try again.')
                ->withInput();
        }
    }

    public function editUser($userId)
    {
        $company = Auth::guard('company')->user();
        $user = \App\Models\User::where('id', $userId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        return view('client.pages.edit-user', compact('company', 'user'));
    }

    public function updateUser(Request $request, $userId)
    {
        $company = Auth::guard('company')->user();
        $user = \App\Models\User::where('id', $userId)
            ->where('company_id', $company->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,user',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            return redirect()->route('client.users')
                ->with('success', 'User updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update user. Please try again.')
                ->withInput();
        }
    }

    public function deleteUser($userId)
    {
        $company = Auth::guard('company')->user();
        $user = \App\Models\User::where('id', $userId)
            ->where('company_id', $company->id)
            ->firstOrFail();

        try {
            $userEmail = $user->email;
            $user->delete();

            return redirect()->route('client.users')
                ->with('success', 'User ' . $userEmail . ' has been removed successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to remove user. Please try again.');
        }
    }

    public function viewReports()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $campaigns = $company->campaigns()->latest()->paginate(10);
        return view('client.pages.reports', compact('company', 'campaigns'));
    }

    public function viewCampaignReport($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        // Get campaign with validation
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with(['targets', 'interactions', 'company'])
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

        // Get target-specific analytics
        $targetAnalytics = $campaign->targets->map(function($target) use ($interactions) {
            $targetInteractions = $interactions->where('email', $target->email);
            return [
                'id' => $target->id,
                'name' => $target->name,
                'email' => $target->email,
                'sent' => $targetInteractions->where('action_type', 'sent')->count(),
                'opened' => $targetInteractions->where('action_type', 'opened')->count(),
                'clicked' => $targetInteractions->where('action_type', 'clicked')->count(),
                'submitted' => $targetInteractions->where('action_type', 'submitted')->count(),
                'risk_level' => $this->calculateRiskLevel($targetInteractions),
            ];
        });

        // Get timeline data
        $timelineData = $this->getTimelineData($campaign, $interactions);

        // Get hourly distribution
        $hourlyData = $this->getHourlyData($interactions);

        return view('client.pages.campaign-report', compact(
            'company',
            'campaign',
            'totalTargets',
            'totalSent',
            'totalOpened',
            'totalClicked',
            'totalSubmitted',
            'totalFailed',
            'openedPercentage',
            'clickedPercentage',
            'submittedPercentage',
            'targetAnalytics',
            'timelineData',
            'hourlyData'
        ));
    }

    private function calculateRiskLevel($interactions)
    {
        if ($interactions->where('action_type', 'submitted')->count() > 0) {
            return 'high';
        } elseif ($interactions->where('action_type', 'clicked')->count() > 0) {
            return 'medium';
        } elseif ($interactions->where('action_type', 'opened')->count() > 0) {
            return 'low';
        }
        return 'none';
    }

    private function getTimelineData($campaign, $interactions)
    {
        $startDate = $campaign->start_date;
        $endDate = $campaign->end_date ?? now();
        $timeline = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayInteractions = $interactions->filter(function ($interaction) use ($dateStr) {
                return $interaction->timestamp->format('Y-m-d') === $dateStr;
            });

            $timeline[] = [
                'date' => $dateStr,
                'sent' => $dayInteractions->where('action_type', 'sent')->count(),
                'opened' => $dayInteractions->where('action_type', 'opened')->count(),
                'clicked' => $dayInteractions->where('action_type', 'clicked')->count(),
                'submitted' => $dayInteractions->where('action_type', 'submitted')->count(),
            ];

            $currentDate->addDay();
        }

        return $timeline;
    }

    private function getHourlyData($interactions)
    {
        $hourly = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourInteractions = $interactions->filter(function ($interaction) use ($hour) {
                return $interaction->timestamp->hour === $hour;
            });

            $hourly[] = [
                'hour' => $hour,
                'opened' => $hourInteractions->where('action_type', 'opened')->count(),
                'clicked' => $hourInteractions->where('action_type', 'clicked')->count(),
                'submitted' => $hourInteractions->where('action_type', 'submitted')->count(),
            ];
        }

        return $hourly;
    }

    public function viewDashboardApi()
    {
        $company = Auth::guard('company')->user();
        return view('client.pages.dashboard-api', compact('company'));
    }

    // Campaign CRUD Methods
    public function indexCampaigns(Request $request)
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        
        $query = $company->campaigns()->with(['targets', 'interactions']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('type', 'like', '%' . $request->search . '%')
                  ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }
        
        $campaigns = $query->latest()->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => $company->campaigns()->count(),
            'active' => $company->campaigns()->whereIn('status', ['active', 'running'])->count(),
            'draft' => $company->campaigns()->where('status', 'draft')->count(),
            'completed' => $company->campaigns()->where('status', 'completed')->count(),
        ];
        
        return view('client.pages.campaigns.index', compact('company', 'campaigns', 'stats'));
    }

    public function storeCampaign(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:phishing,awareness,training',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $campaign = Campaign::create([
            'company_id' => $company->id,
            'type' => $request->type,
            'email_template_id' => $request->email_template_id,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', 'Campaign created successfully!');
    }

    public function showCampaign($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with(['targets', 'interactions', 'emailTemplate'])
            ->firstOrFail();
        
        // Get statistics
        $totalTargets = $campaign->targets->count();
        $interactions = $campaign->interactions;
        $totalSent = $interactions->where('action_type', 'sent')->count();
        $totalOpened = $interactions->where('action_type', 'opened')->count();
        $totalClicked = $interactions->where('action_type', 'clicked')->count();
        $totalSubmitted = $interactions->where('action_type', 'submitted')->count();
        
        // Get submitted credentials data with all available information
        $submittedCredentials = $interactions->where('action_type', 'submitted')
            ->map(function($interaction) use ($campaign) {
                $target = $campaign->targets->firstWhere('email', $interaction->email);
                $metadata = $interaction->metadata ?? [];
                
                // Get template name from metadata, campaign email template, or default
                $templateName = 'N/A';
                if (!empty($metadata['template_name'])) {
                    $templateName = $metadata['template_name'];
                } elseif ($campaign->emailTemplate) {
                    $templateName = $campaign->emailTemplate->name;
                }
                
                return [
                    'interaction_id' => $interaction->id,
                    'target_id' => $target ? $target->id : null,
                    'target_name' => $target ? $target->name : 'Unknown',
                    'email' => $interaction->email,
                    'timestamp' => $interaction->timestamp,
                    'submitted_at' => $interaction->timestamp->format('M d, Y H:i:s'),
                    'time_ago' => $interaction->timestamp->diffForHumans(),
                    'department' => !empty($metadata['department']) ? $metadata['department'] : ($target && $target->department ? $target->department : 'N/A'),
                    'ip_address' => !empty($metadata['ip_address']) ? $metadata['ip_address'] : 'N/A',
                    'user_agent' => !empty($metadata['user_agent']) ? $metadata['user_agent'] : 'N/A',
                    'campaign_type' => !empty($metadata['campaign_type']) ? $metadata['campaign_type'] : $campaign->type,
                    'template_name' => $templateName,
                    'password' => $metadata['password'] ?? null,
                    'password_provided' => isset($metadata['password_provided']) ? (bool)$metadata['password_provided'] : false,
                    'metadata' => $metadata,
                ];
            })
            ->sortByDesc('timestamp')
            ->values();
        
        return view('client.pages.campaigns.show', compact(
            'company',
            'campaign',
            'totalTargets',
            'totalSent',
            'totalOpened',
            'totalClicked',
            'totalSubmitted',
            'submittedCredentials'
        ));
    }

    public function editCampaign($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        $templates = \App\Models\EmailTemplate::orderBy('type')->orderBy('name')->get();
        
        return view('client.pages.campaigns.edit', compact('company', 'campaign', 'templates'));
    }

    public function updateCampaign(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        // Don't allow editing if campaign is active or completed
        if (in_array($campaign->status, ['active', 'running', 'completed'])) {
            return redirect()->back()
                ->with('error', 'Cannot edit ' . $campaign->status . ' campaigns.');
        }
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:phishing,awareness,training',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $campaign->update([
            'type' => $request->type,
            'email_template_id' => $request->email_template_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', 'Campaign updated successfully!');
    }

    public function destroyCampaign($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        // Don't allow deleting if campaign is active
        if (in_array($campaign->status, ['active', 'running'])) {
            return redirect()->back()
                ->with('error', 'Cannot delete active campaigns. Please stop the campaign first.');
        }
        
        $campaign->delete();
        
            return redirect()->route('client.campaigns.index')
                ->with('success', 'Campaign deleted successfully!');
    }

    // Company Management Methods (Admin Only)
    public function indexCompanies(Request $request)
    {
        $currentUser = Auth::guard('company')->user();
        
        // Check if user is admin
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $query = Company::with('plan');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $perPage = $request->get('per_page', 15);
        $companies = $query->latest()->paginate($perPage);
        
        // Get total statistics
        $stats = [
            'total' => Company::count(),
            'with_plans' => Company::whereNotNull('plan_id')->count(),
            'active_campaigns' => Company::withCount('campaigns')->get()->sum('campaigns_count'),
        ];
        
        return view('admin.companies.index', compact('companies', 'stats', 'currentUser'));
    }

    public function createCompany()
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $plans = Plan::all();
        return view('admin.companies.create', compact('plans', 'currentUser'));
    }

    public function storeCompany(Request $request)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'plan_id' => 'required|integer|exists:plans,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => $request->plan_id,
        ]);
        
        return redirect()->route('admin.companies.show', $company->id)
            ->with('success', 'Company created successfully!');
    }

    public function showCompany($companyId)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $company = Company::with(['plan', 'campaigns', 'users', 'payments'])->findOrFail($companyId);
        
        // Calculate statistics
        $campaignsCount = $company->campaigns()->count();
        $usersCount = $company->users()->count();
        $paymentsCount = $company->payments()->count();
        $totalSpent = $company->payments()->where('status', 'completed')->sum('amount');
        
        // Get recent campaigns
        $recentCampaigns = $company->campaigns()->latest()->limit(5)->get();
        
        return view('admin.companies.show', compact(
            'company',
            'campaignsCount',
            'usersCount',
            'paymentsCount',
            'totalSpent',
            'recentCampaigns',
            'currentUser'
        ));
    }

    public function editCompany($companyId)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $company = Company::with('plan')->findOrFail($companyId);
        $plans = Plan::all();
        
        return view('admin.companies.edit', compact('company', 'plans', 'currentUser'));
    }

    public function updateCompany(Request $request, $companyId)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $company = Company::findOrFail($companyId);
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:companies,email,' . $companyId . '|max:255',
            'plan_id' => 'sometimes|required|integer|exists:plans,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $updateData = $request->only(['name', 'email', 'plan_id']);
        
        if (!empty($updateData)) {
            $company->update($updateData);
        }
        
        return redirect()->route('admin.companies.show', $company->id)
            ->with('success', 'Company updated successfully!');
    }

    public function destroyCompany($companyId)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $company = Company::findOrFail($companyId);
        
        // Prevent deleting own account
        if ($company->id === $currentUser->id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }
        
        $company->delete();
        
        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully!');
    }

    public function companyStatistics($companyId)
    {
        $currentUser = Auth::guard('company')->user();
        
        if ($currentUser->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        
        $company = Company::with(['plan', 'campaigns.targets', 'campaigns.interactions'])->findOrFail($companyId);
        
        // Calculate comprehensive statistics
        $totalCampaigns = $company->campaigns()->count();
        $totalTargets = $company->campaigns()->withCount('targets')->get()->sum('targets_count');
        $totalInteractions = $company->campaigns()->withCount('interactions')->get()->sum('interactions_count');
        
        // Calculate vulnerability rates
        $campaignsWithSubmits = $company->campaigns()->with('interactions')->get();
        $totalSubmits = 0;
        $totalClicks = 0;
        $totalOpens = 0;
        
        foreach ($campaignsWithSubmits as $campaign) {
            $totalSubmits += $campaign->interactions->where('action_type', 'submitted')->count();
            $totalClicks += $campaign->interactions->where('action_type', 'clicked')->count();
            $totalOpens += $campaign->interactions->where('action_type', 'opened')->count();
        }
        
        $averageVulnerabilityRate = $totalTargets > 0 ? round(($totalSubmits / $totalTargets) * 100, 2) : 0;
        
        // Get campaign performance
        $campaignPerformance = $company->campaigns()->with(['targets', 'interactions'])->get()->map(function($campaign) {
            $targetsCount = $campaign->targets->count();
            $submitsCount = $campaign->interactions->where('action_type', 'submitted')->count();
            $vulnerabilityRate = $targetsCount > 0 ? round(($submitsCount / $targetsCount) * 100, 2) : 0;
            
            return [
                'campaign_id' => $campaign->id,
                'type' => $campaign->type,
                'status' => $campaign->status,
                'vulnerability_rate' => $vulnerabilityRate,
                'targets_count' => $targetsCount,
                'interactions_count' => $campaign->interactions->count(),
            ];
        });
        
        return view('admin.companies.statistics', compact(
            'company',
            'totalCampaigns',
            'totalTargets',
            'totalInteractions',
            'averageVulnerabilityRate',
            'campaignPerformance',
            'totalSubmits',
            'totalClicks',
            'totalOpens',
            'currentUser'
        ));
    }

    // Campaign Management Methods
    public function addTargetsForm($campaignId)
    {
        $company = Auth::guard('company')->user();
        $company->load('plan');
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with('targets')
            ->firstOrFail();
        
        return view('client.pages.campaigns.add-targets', compact('company', 'campaign'));
    }

    public function storeTargets(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        $company->load('plan');
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'targets' => 'required|array|min:1',
            'targets.*.name' => 'required|string|max:255',
            'targets.*.email' => 'required|email|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
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
            
            return redirect()->back()
                ->with('error', "You have reached the limit for your plan ({$plan->name}). Your plan allows {$employeeLimit} employees/targets. You currently have {$currentTotalTargets} targets. " . 
                    ($remainingSlots > 0 ? "You can add up to {$remainingSlots} more target(s)." : "You cannot add more targets. Please upgrade your plan to add more targets."))
                ->withInput();
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
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', count($targets) . ' target(s) added successfully!');
    }

    public function campaignStats($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with(['targets', 'interactions'])
            ->firstOrFail();
        
        $stats = $this->emailService->getCampaignStats($campaign);
        
        return view('client.pages.campaigns.stats', compact('company', 'campaign', 'stats'));
    }

    public function campaignAiAnalysis($campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with(['targets', 'interactions'])
            ->firstOrFail();
        
        $analysis = $this->aiAnalysisService->analyzeCampaign($campaign->id);
        
        return view('client.pages.campaigns.ai-analysis', compact('company', 'campaign', 'analysis'));
    }

    public function sendCampaignEmails(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        
        Log::info('📧 [EMAIL SEND] Starting email send process', [
            'campaign_id' => $campaignId,
            'company_id' => $company->id,
            'company_name' => $company->name,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->with('targets')
            ->firstOrFail();
        
        Log::info('📧 [EMAIL SEND] Campaign found', [
            'campaign_id' => $campaign->id,
            'campaign_type' => $campaign->type,
            'campaign_status' => $campaign->status,
            'targets_count' => $campaign->targets->count()
        ]);
        
        if ($campaign->status !== 'active' && $campaign->status !== 'draft') {
            Log::warning('📧 [EMAIL SEND] Invalid campaign status', [
                'campaign_id' => $campaign->id,
                'status' => $campaign->status,
                'allowed_statuses' => ['active', 'draft']
            ]);
            return redirect()->back()
                ->with('error', 'Only active or draft campaigns can send emails.');
        }
        
        if ($campaign->targets->isEmpty()) {
            Log::warning('📧 [EMAIL SEND] No targets found', [
                'campaign_id' => $campaign->id
            ]);
            return redirect()->back()
                ->with('error', 'No targets found for this campaign. Please add targets first.');
        }
        
        // Log mail configuration
        Log::info('📧 [EMAIL SEND] Mail configuration', [
            'mail_driver' => config('mail.default'),
            'mail_host' => config('mail.mailers.smtp.host'),
            'mail_port' => config('mail.mailers.smtp.port'),
            'mail_encryption' => config('mail.mailers.smtp.encryption'),
            'mail_username' => config('mail.mailers.smtp.username') ? '***' : 'Not set',
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'queue_connection' => config('queue.default'),
        ]);
        
        try {
            Log::info('📧 [EMAIL SEND] Calling EmailService::sendCampaignEmails', [
                'campaign_id' => $campaign->id,
                'targets_count' => $campaign->targets->count()
            ]);
            
            $results = $this->emailService->sendCampaignEmails($campaign);
            
            Log::info('📧 [EMAIL SEND] EmailService completed', [
                'campaign_id' => $campaign->id,
                'success_count' => $results['success'],
                'failed_count' => $results['failed'],
                'errors' => $results['errors']
            ]);
            
            $message = 'Campaign emails queued successfully! ' . $results['success'] . ' email(s) queued.';
            if ($results['failed'] > 0) {
                $message .= ' ' . $results['failed'] . ' email(s) failed.';
            }
            
            // Send Telegram notification if enabled
            if ($company->telegram_enabled) {
                $telegramMessage = "📧 <b>Campaign Emails Sent</b>\n\n" .
                                 "Campaign: <b>{$campaign->type}</b>\n" .
                                 "Emails Queued: {$results['success']}\n" .
                                 "Failed: {$results['failed']}\n" .
                                 "Time: " . now()->format('Y-m-d H:i:s');
                $this->telegramService->sendMessage($company, $telegramMessage);
            }
            
            return redirect()->route('client.campaigns.show', $campaign->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('📧 [EMAIL SEND] Exception occurred', [
                'campaign_id' => $campaign->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to send campaign emails: ' . $e->getMessage());
        }
    }

    public function resendEmailToTarget(Request $request, $campaignId, $targetId)
    {
        $company = Auth::guard('company')->user();
        
        Log::info('📧 [RESEND EMAIL] Starting resend process', [
            'campaign_id' => $campaignId,
            'target_id' => $targetId,
            'company_id' => $company->id,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        $target = CampaignTarget::where('id', $targetId)
            ->where('campaign_id', $campaignId)
            ->firstOrFail();
        
        Log::info('📧 [RESEND EMAIL] Campaign and target found', [
            'campaign_id' => $campaign->id,
            'campaign_type' => $campaign->type,
            'target_id' => $target->id,
            'target_email' => $target->email,
            'target_name' => $target->name
        ]);
        
        try {
            $result = $this->emailService->resendEmail($campaign, $target);
            
            Log::info('📧 [RESEND EMAIL] EmailService result', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'success' => $result['success'],
                'message' => $result['message']
            ]);
            
            if ($result['success']) {
                return redirect()->route('client.campaigns.show', $campaign->id)
                    ->with('success', 'Email resent successfully to ' . $target->email . '!');
            } else {
                return redirect()->back()
                    ->with('error', 'Failed to resend email: ' . ($result['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('📧 [RESEND EMAIL] Exception occurred', [
                'campaign_id' => $campaign->id,
                'target_email' => $target->email,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to resend email: ' . $e->getMessage());
        }
    }

    public function launchCampaign(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        if ($campaign->status !== 'draft' && $campaign->status !== 'paused') {
            return redirect()->back()
                ->with('error', 'Only draft or paused campaigns can be launched.');
        }
        
        // Check if campaign has targets before launching
        if ($campaign->targets->count() === 0) {
            return redirect()->back()
                ->with('error', 'Cannot launch campaign without targets. Please add targets first.');
        }
        
        $campaign->update(['status' => 'active']);
        
        // Send Telegram notification if enabled
        if ($company->telegram_enabled) {
            $message = $this->telegramService->formatCampaignMessage('launched', [
                'campaign_name' => $campaign->type,
                'campaign_type' => ucfirst($campaign->type),
                'targets_count' => $campaign->targets->count(),
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
            $this->telegramService->sendMessage($company, $message);
        }
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', 'Campaign launched successfully!');
    }

    public function pauseCampaign(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        if (!in_array($campaign->status, ['active', 'running'])) {
            return redirect()->back()
                ->with('error', 'Only active campaigns can be paused.');
        }
        
        $campaign->update(['status' => 'paused']);
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', 'Campaign paused successfully!');
    }

    public function stopCampaign(Request $request, $campaignId)
    {
        $company = Auth::guard('company')->user();
        
        $campaign = Campaign::where('id', $campaignId)
            ->where('company_id', $company->id)
            ->firstOrFail();
        
        if (!in_array($campaign->status, ['active', 'running', 'paused'])) {
            return redirect()->back()
                ->with('error', 'Only active or paused campaigns can be stopped.');
        }
        
        $campaign->update(['status' => 'completed']);
        
        return redirect()->route('client.campaigns.show', $campaign->id)
            ->with('success', 'Campaign stopped successfully!');
    }

    /**
     * Show the upgrade plan page
     */
    public function upgradePlan()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        // Get all available plans
        $plans = Plan::orderBy('price', 'asc')->get();
        
        return view('client.pages.upgrade-plan', compact('company', 'plans'));
    }

    /**
     * Handle plan upgrade checkout
     */
    public function upgradeCheckout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        $selectedPlan = Plan::findOrFail($request->plan_id);
        
        // Check if it's a valid upgrade
        $selectedPrice = $selectedPlan->getPriceFloat();
        $currentPrice = $company->plan->getPriceFloat();
        if ($selectedPrice <= $currentPrice) {
            return redirect()->route('client.upgrade-plan')
                ->with('error', 'You can only upgrade to a higher priced plan.');
        }

        // Check if trying to upgrade to the same plan
        if ($selectedPlan->id === $company->plan_id) {
            return redirect()->route('client.upgrade-plan')
                ->with('error', 'You are already on this plan.');
        }

        try {
            $paymentService = app(\App\Services\PaymentService::class);
            $result = $paymentService->initializePayment($company->id, $request->plan_id);
            
            // Redirect to checkout page
            return redirect($result['checkout_url']);
        } catch (\Exception $e) {
            return redirect()->route('client.upgrade-plan')
                ->with('error', 'Failed to initialize payment: ' . $e->getMessage());
        }
    }

    /**
     * Show the contact support page
     */
    public function contactSupport()
    {
        $company = null;
        if (Auth::guard('company')->check()) {
            $company = Auth::guard('company')->user();
        }
        
        return view('client.pages.contact-support', compact('company'));
    }

    /**
     * Handle contact support form submission
     */
    public function submitContactSupport(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        // If user is authenticated, use their company info
        if (Auth::guard('company')->check()) {
            $company = Auth::guard('company')->user();
            $request->merge([
                'name' => $company->name,
                'email' => $company->email,
            ]);
        }

        try {
            // Here you can:
            // 1. Send email to support team
            // 2. Store in database (support_tickets table)
            // 3. Create notification
            
            // For now, we'll just log it and show success message
            \Illuminate\Support\Facades\Log::info('Support Request Submitted', [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'priority' => $request->priority ?? 'medium',
                'message' => $request->message,
                'company_id' => Auth::guard('company')->check() ? Auth::guard('company')->id() : null,
            ]);

            // TODO: Send email notification to support team
            // Mail::to(config('mail.support_email', 'support@example.com'))->send(new SupportTicketMail($request->all()));

            return redirect()->back()
                ->with('success', 'Thank you for contacting us! We have received your message and will get back to you within 24 hours.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Support Request Failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Sorry, there was an error submitting your request. Please try again or email us directly.')
                ->withInput();
        }
    }

    /**
     * Handle chatbot message
     */
    public function chatbotMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->message;
        $company = null;
        $userName = 'Guest';
        $userEmail = null;

        if (Auth::guard('company')->check()) {
            $company = Auth::guard('company')->user();
            $userName = $company->name;
            $userEmail = $company->email;
        }

        // Simple chatbot responses based on keywords
        $response = $this->generateChatbotResponse($userMessage, $company);

        // Log the conversation
        \Illuminate\Support\Facades\Log::info('Chatbot Conversation', [
            'user' => $userName,
            'email' => $userEmail,
            'message' => $userMessage,
            'response' => $response,
            'company_id' => $company ? $company->id : null,
        ]);

        return response()->json([
            'success' => true,
            'response' => $response,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Generate chatbot response based on user message
     */
    private function generateChatbotResponse(string $message, $company = null): string
    {
        $message = strtolower($message);
        
        // Greeting responses
        if (preg_match('/\b(hi|hello|hey|greetings|good morning|good afternoon|good evening)\b/', $message)) {
            return "Hello! 👋 I'm here to help you with any questions about our phishing simulation platform. How can I assist you today?";
        }

        // Pricing/Plan questions
        if (preg_match('/\b(price|pricing|cost|plan|subscription|billing|payment|upgrade|downgrade)\b/', $message)) {
            $planLink = route('client.upgrade-plan');
            return "We offer several subscription plans to fit your needs. You can view all available plans and pricing on our Upgrade Plan page: {$planLink}\n\nWould you like to know more about a specific plan?";
        }

        // Feature questions
        if (preg_match('/\b(feature|features|what can|capabilities|functionality|how does|how to)\b/', $message)) {
            return "Our platform offers comprehensive phishing simulation features including:\n\n• Email template library\n• Campaign management\n• Detailed analytics and reports\n• Employee training resources\n• AI-powered insights\n\nWould you like more details about any specific feature?";
        }

        // Campaign questions
        if (preg_match('/\b(campaign|campaigns|create campaign|launch|send email|phishing)\b/', $message)) {
            $campaignLink = route('client.campaigns.create');
            return "You can create and manage phishing campaigns from your dashboard. To create a new campaign, visit the Create Campaign page: {$campaignLink}\n\nNeed help with campaign setup?";
        }

        // Support/Help questions
        if (preg_match('/\b(help|support|problem|issue|error|bug|not working|trouble|stuck)\b/', $message)) {
            $supportLink = route('contact-support');
            return "I'm here to help! For detailed assistance, you can:\n\n• Fill out our Contact Support form: {$supportLink}\n• Email us at support@phishingsim.com\n• Call us at +1-800-555-0123\n\nWhat specific issue are you experiencing?";
        }

        // Account questions
        if (preg_match('/\b(account|profile|settings|dashboard|login|password|reset)\b/', $message)) {
            if ($company) {
                $dashboardLink = route('client.dashboard');
                return "You can manage your account settings from your Dashboard: {$dashboardLink}\n\nFor password resets or account issues, please contact our support team.";
            } else {
                return "For account-related questions, please log in to your account or contact our support team for assistance.";
            }
        }

        // Report/Analytics questions
        if (preg_match('/\b(report|reports|analytics|statistics|stats|data|results|performance)\b/', $message)) {
            if ($company) {
                $reportsLink = route('client.reports');
                return "You can view detailed reports and analytics from the Reports page: {$reportsLink}\n\nThis includes campaign performance, employee engagement, and security metrics.";
            } else {
                return "Our platform provides comprehensive reporting and analytics for all your phishing simulation campaigns. Log in to access your reports.";
            }
        }

        // Default response
        return "Thank you for your message! I understand you're asking about: \"" . htmlspecialchars($message) . "\"\n\nFor more detailed assistance, I recommend:\n\n• Checking our documentation\n• Contacting our support team via the Contact Support form\n• Email: support@phishingsim.com\n\nIs there anything specific I can help you with about our platform?";
    }

    /**
     * Show the billing page
     */
    public function billing()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        // Get payment history
        $paymentService = app(\App\Services\PaymentService::class);
        $paymentHistory = $paymentService->getPaymentHistory($company->id);
        
        // Get all payments for statistics (not paginated)
        $allPayments = \App\Models\Payment::where('company_id', $company->id)->get();
        
        // Get paginated payments with plan relationship for display
        $payments = \App\Models\Payment::where('company_id', $company->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Calculate billing statistics
        $stats = [
            'total_payments' => $paymentHistory['total_payments'],
            'total_amount' => $paymentHistory['total_amount'],
            'completed_payments' => $allPayments->where('status', 'completed')->count(),
            'pending_payments' => $allPayments->where('status', 'pending')->count(),
            'failed_payments' => $allPayments->where('status', 'failed')->count(),
            'monthly_spending' => $allPayments->where('status', 'completed')
                ->filter(function($payment) {
                    return $payment->created_at >= now()->subMonth();
                })
                ->sum('amount'),
        ];
        
        return view('client.pages.billing', compact('company', 'payments', 'stats'));
    }

    /**
     * Show the profile page
     */
    public function showProfile()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        return view('client.pages.profile', compact('company'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::findOrFail($company->id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies,email,' . $company->id,
        ]);

        $company->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('client.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::findOrFail($company->id);
        
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $company->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'The current password is incorrect.'])
                ->withInput();
        }

        // Update password
        $company->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('client.profile')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show the settings page
     */
    public function showSettings()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        return view('client.pages.settings', compact('company'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::findOrFail($company->id);
        
        $section = $request->input('section', 'general');
        
        // Validate based on section
        switch ($section) {
            case 'notifications':
                $request->validate([
                    'notify_campaign_updates' => 'boolean',
                    'notify_email_reports' => 'boolean',
                    'notify_high_risk' => 'boolean',
                    'notify_billing' => 'boolean',
                    'telegram_enabled' => 'boolean',
                    'telegram_bot_token' => 'nullable|string|max:255',
                    'telegram_chat_id' => 'nullable|string|max:255',
                ]);

                // Update Telegram settings if provided
                if ($request->has('telegram_enabled')) {
                    $company->telegram_enabled = $request->boolean('telegram_enabled');
                    
                    if ($request->boolean('telegram_enabled')) {
                        $request->validate([
                            'telegram_bot_token' => 'required|string',
                            'telegram_chat_id' => 'required|string',
                        ]);
                        $company->telegram_bot_token = $request->input('telegram_bot_token');
                        $company->telegram_chat_id = $request->input('telegram_chat_id');
                    } else {
                        // Optionally clear token and chat_id when disabled
                        // $company->telegram_bot_token = null;
                        // $company->telegram_chat_id = null;
                    }
                    $company->save();
                }

                return redirect()->route('client.settings')
                    ->with('success', 'Notification settings saved successfully!');
                
            case 'email':
                $request->validate([
                    'email_frequency' => 'nullable|in:realtime,daily,weekly,never',
                    'email_marketing' => 'boolean',
                    'email_newsletter' => 'boolean',
                ]);
                // Store email preferences
                return redirect()->route('client.settings')
                    ->with('success', 'Email preferences saved successfully!');
                
            case 'general':
                $request->validate([
                    'timezone' => 'nullable|string|max:50',
                    'date_format' => 'nullable|string|max:20',
                    'language' => 'nullable|string|max:10',
                ]);
                // Store general preferences
                return redirect()->route('client.settings')
                    ->with('success', 'General settings saved successfully!');
                
            default:
                return redirect()->route('client.settings')
                    ->with('success', 'Settings saved successfully!');
        }
    }

    /**
     * Test Telegram connection
     */
    public function testTelegramConnection(Request $request)
    {
        $request->validate([
            'bot_token' => 'required|string',
            'chat_id' => 'required|string',
        ]);

        $result = $this->telegramService->testConnection(
            $request->input('bot_token'),
            $request->input('chat_id')
        );

        return response()->json($result);
    }

    /**
     * View all notifications
     */
    public function viewNotifications()
    {
        /** @var \App\Models\Company $company */
        $company = Auth::guard('company')->user();
        $company = Company::with('plan')->findOrFail($company->id);
        
        // Get notifications
        $notifications = $this->getNotifications($company);
        
        return view('client.pages.notifications', compact('company', 'notifications'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        // This can be extended to mark notifications as read in database
        return redirect()->back()
            ->with('success', 'All notifications marked as read!');
    }

    /**
     * Get notifications for company
     */
    private function getNotifications($company)
    {
        $notifications = [];
        
        // Recent campaign activities
        $recentCampaigns = $company->campaigns()->latest()->limit(10)->get();
        foreach ($recentCampaigns as $campaign) {
            if ($campaign->status === 'active') {
                $notifications[] = [
                    'type' => 'campaign',
                    'title' => 'Campaign Active',
                    'message' => "Campaign '{$campaign->type}' is now active",
                    'time' => $campaign->updated_at,
                    'icon' => '📧',
                    'link' => route('client.campaigns.show', $campaign->id),
                    'read' => false
                ];
            }
        }
        
        // Recent high-risk interactions
        $recentInteractions = \App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
            $q->where('company_id', $company->id);
        })->where('action_type', 'submitted')
          ->latest()
          ->limit(10)
          ->get();
        
        foreach ($recentInteractions as $interaction) {
            $notifications[] = [
                'type' => 'interaction',
                'title' => 'High-Risk Alert',
                'message' => "Employee submitted credentials in campaign",
                'time' => $interaction->created_at,
                'icon' => '⚠️',
                'link' => route('client.campaigns.show', $interaction->campaign_id),
                'read' => false
            ];
        }
        
        // Sort by time
        usort($notifications, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        return $notifications;
    }
}



