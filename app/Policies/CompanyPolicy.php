<?php

namespace App\Policies;

use App\Models\Company;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    /**
     * Determine whether the company can view its own data.
     */
    public function view(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }

    /**
     * Determine whether the company can update its own data.
     */
    public function update(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }

    /**
     * Determine whether the company can delete its own data.
     */
    public function delete(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }

    /**
     * Determine whether the company can view its dashboard.
     */
    public function viewDashboard(Company $company): bool
    {
        return true; // Companies can view their own dashboard
    }

    /**
     * Determine whether the company can view its payment history.
     */
    public function viewPayments(Company $company, Company $model): bool
    {
        return $company->id === $model->id;
    }

    /**
     * Determine whether the company can make payments.
     */
    public function makePayment(Company $company): bool
    {
        return true; // Companies can make payments
    }
}
