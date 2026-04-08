<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Initialize payment and return checkout URL
     */
    public function initializePayment(int $companyId, int $planId): array
    {
        $plan = Plan::findOrFail($planId);
        
        // Generate a unique transaction ID
        $transactionId = 'txn_' . Str::random(20);
        
        // Create payment record
        $payment = Payment::create([
            'company_id' => $companyId,
            'plan_id' => $planId,
            'amount' => $plan->price,
            'status' => 'pending',
            'transaction_id' => $transactionId,
        ]);

        // Simulate payment gateway integration
        // In a real implementation, this would integrate with Stripe, PayPal, etc.
        $checkoutUrl = $this->generateCheckoutUrl($transactionId, $plan);

        return [
            'payment_id' => $payment->id,
            'transaction_id' => $transactionId,
            'checkout_url' => $checkoutUrl,
            'amount' => $plan->price,
            'plan' => $plan,
        ];
    }

    /**
     * Confirm payment and update company plan
     */
    public function confirmPayment(string $transactionId): array
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'pending')
            ->firstOrFail();

        // Simulate payment verification
        // In a real implementation, this would verify with the payment gateway
        $isPaymentSuccessful = $this->verifyPaymentWithGateway($transactionId);

        if ($isPaymentSuccessful) {
            // Update payment status
            $payment->update(['status' => 'completed']);

            // Update company plan
            $company = $payment->company;
            $company->update(['plan_id' => $payment->plan_id]);

            return [
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'payment' => $payment,
                'company' => $company,
            ];
        } else {
            // Update payment status to failed
            $payment->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => 'Payment verification failed',
                'payment' => $payment,
            ];
        }
    }

    /**
     * Generate checkout URL (simulated)
     */
    private function generateCheckoutUrl(string $transactionId, Plan $plan): string
    {
        // In a real implementation, this would generate a Stripe/PayPal checkout URL
        $baseUrl = config('app.url');
        return "{$baseUrl}/checkout/{$transactionId}?plan={$plan->name}&amount={$plan->price}";
    }

    /**
     * Verify payment with gateway (simulated)
     */
    private function verifyPaymentWithGateway(string $transactionId): bool
    {
        // In a real implementation, this would verify with the actual payment gateway
        // For simulation, we'll randomly return success/failure (90% success rate)
        return rand(1, 10) <= 9;
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): array
    {
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();

        return [
            'transaction_id' => $payment->transaction_id,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'plan' => $payment->plan,
            'created_at' => $payment->created_at,
        ];
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(string $transactionId): array
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $payment->update(['status' => 'cancelled']);

        return [
            'success' => true,
            'message' => 'Payment cancelled successfully',
            'payment' => $payment,
        ];
    }

    /**
     * Get company payment history
     */
    public function getPaymentHistory(int $companyId): array
    {
        $payments = Payment::where('company_id', $companyId)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'payments' => $payments,
            'total_payments' => $payments->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
        ];
    }
}
