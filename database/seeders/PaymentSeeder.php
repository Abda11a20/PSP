<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Company;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💳 Seeding payments...');

        $companies = Company::where('plan_id', '>', 1)->get(); // Only paid plans

        foreach ($companies as $company) {
            $this->createPaymentsForCompany($company);
        }

        $this->command->info('✅ Payments seeded successfully');
    }

    /**
     * Create payments for a specific company
     */
    private function createPaymentsForCompany(Company $company): void
    {
        $plan = Plan::find($company->plan_id);
        
        // Create initial payment (when they signed up)
        $this->createPayment($company, $plan, 'completed', $company->created_at);

        // Create recurring payments if company is older than 30 days
        if ($company->created_at->lt(now()->subDays(30))) {
            $this->createRecurringPayments($company, $plan);
        }

        // Create some failed payments for testing
        if (rand(1, 3) === 1) { // 1 in 3 chance
            $this->createFailedPayment($company, $plan);
        }
    }

    /**
     * Create a payment record
     */
    private function createPayment(Company $company, Plan $plan, string $status, $createdAt): void
    {
        Payment::create([
            'company_id' => $company->id,
            'plan_id' => $company->plan_id,
            'amount' => $plan->price,
            'status' => $status,
            'transaction_id' => $this->generateTransactionId($status),
            'created_at' => $createdAt,
            'updated_at' => now(),
        ]);
    }

    /**
     * Create recurring payments
     */
    private function createRecurringPayments(Company $company, Plan $plan): void
    {
        $paymentCount = $this->getRecurringPaymentCount($company);
        
        for ($i = 1; $i <= $paymentCount; $i++) {
            $paymentDate = $company->created_at->copy()->addMonths($i);
            
            // Skip if payment date is in the future
            if ($paymentDate->isFuture()) {
                continue;
            }

            $status = $this->determinePaymentStatus($i);
            $this->createPayment($company, $plan, $status, $paymentDate);
        }
    }

    /**
     * Create a failed payment
     */
    private function createFailedPayment(Company $company, Plan $plan): void
    {
        $failedDate = $company->created_at->copy()->addDays(rand(15, 45));
        
        if ($failedDate->isPast()) {
            $this->createPayment($company, $plan, 'failed', $failedDate);
        }
    }

    /**
     * Get recurring payment count based on company age
     */
    private function getRecurringPaymentCount(Company $company): int
    {
        $monthsSinceCreated = $company->created_at->diffInMonths(now());
        return min($monthsSinceCreated, 12); // Max 12 months of history
    }

    /**
     * Determine payment status
     */
    private function determinePaymentStatus(int $paymentNumber): string
    {
        // Most payments are completed
        if (rand(1, 10) <= 8) {
            return 'completed';
        }

        // Some are pending
        if (rand(1, 10) <= 9) {
            return 'pending';
        }

        // Few are failed
        return 'failed';
    }

    /**
     * Generate transaction ID
     */
    private function generateTransactionId(string $status): string
    {
        $prefix = match ($status) {
            'completed' => 'txn_completed_',
            'pending' => 'txn_pending_',
            'failed' => 'txn_failed_',
            default => 'txn_',
        };

        return $prefix . uniqid();
    }
}

