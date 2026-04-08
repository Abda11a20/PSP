<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\Company;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
{
    /**
     * Determine whether the company can view any models.
     */
    public function viewAny(Company $company): bool
    {
        return true; // Companies can view their own campaigns
    }

    /**
     * Determine whether the company can view the model.
     */
    public function view(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can create models.
     */
    public function create(Company $company): bool
    {
        return true; // Companies can create campaigns
    }

    /**
     * Determine whether the company can update the model.
     */
    public function update(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can delete the model.
     */
    public function delete(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can send emails for the campaign.
     */
    public function sendEmails(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can view campaign statistics.
     */
    public function viewStats(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can view AI analysis.
     */
    public function viewAiAnalysis(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can add targets to the campaign.
     */
    public function addTargets(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }

    /**
     * Determine whether the company can resend emails to targets.
     */
    public function resendEmail(Company $company, Campaign $campaign): bool
    {
        return $company->id === $campaign->company_id;
    }
}
