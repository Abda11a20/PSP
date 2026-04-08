<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\Company;
use App\Policies\CampaignPolicy;
use App\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Campaign::class => CampaignPolicy::class,
        Company::class => CompanyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('view-company-dashboard', function (Company $company) {
            return true; // Companies can view their own dashboard
        });

        Gate::define('manage-campaigns', function (Company $company, Campaign $campaign) {
            return $company->id === $campaign->company_id;
        });
    }
}
