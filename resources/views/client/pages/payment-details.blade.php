@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 900px; margin: 2rem auto;">
        <!-- Header -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Payment Details</h1>
                    <p style="color: #718096;">Transaction ID: <code style="background: #f7fafc; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ $payment->transaction_id }}</code></p>
                </div>
                <a href="{{ route('client.billing') }}" class="btn btn-secondary">
                    ← Back to Billing
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <!-- Main Content -->
            <div>
                <!-- Payment Status Card -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                        @if($payment->status === 'completed')
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 2rem; color: white;">✓</span>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: #10b981;">Payment Completed</h2>
                                <p style="color: #718096; margin: 0.25rem 0 0 0;">Your payment was successfully processed</p>
                            </div>
                        @elseif($payment->status === 'pending')
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 2rem; color: white;">⏳</span>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: #f59e0b;">Payment Pending</h2>
                                <p style="color: #718096; margin: 0.25rem 0 0 0;">Your payment is being processed</p>
                            </div>
                        @elseif($payment->status === 'failed')
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 2rem; color: white;">✗</span>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: #ef4444;">Payment Failed</h2>
                                <p style="color: #718096; margin: 0.25rem 0 0 0;">Your payment could not be processed</p>
                            </div>
                        @elseif($payment->status === 'cancelled')
                            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 2rem; color: white;">⊘</span>
                            </div>
                            <div>
                                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0; color: #6b7280;">Payment Cancelled</h2>
                                <p style="color: #718096; margin: 0.25rem 0 0 0;">This payment was cancelled</p>
                            </div>
                        @endif
                    </div>

                    @if($payment->status === 'pending')
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <a href="{{ route('checkout', $payment->transaction_id) }}" class="btn btn-primary">
                                Complete Payment
                            </a>
                            <form action="{{ route('payment.cancel', $payment->transaction_id) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Are you sure you want to cancel this payment?');">
                                @csrf
                                <button type="submit" class="btn btn-secondary">
                                    Cancel Payment
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Payment Information -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">Payment Information</h3>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Payment ID:</span>
                            <span style="color: #2d3748; font-weight: 600;">#{{ $payment->id }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Transaction ID:</span>
                            <code style="background: #f7fafc; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #2d3748;">{{ $payment->transaction_id }}</code>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Amount:</span>
                            <span style="color: #2d3748; font-weight: 600; font-size: 1.125rem;">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Status:</span>
                            @if($payment->status === 'completed')
                                <span style="padding: 0.25rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    ✓ Completed
                                </span>
                            @elseif($payment->status === 'pending')
                                <span style="padding: 0.25rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    ⏳ Pending
                                </span>
                            @elseif($payment->status === 'failed')
                                <span style="padding: 0.25rem 0.75rem; background: #fee2e2; color: #991b1b; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    ✗ Failed
                                </span>
                            @elseif($payment->status === 'cancelled')
                                <span style="padding: 0.25rem 0.75rem; background: #f3f4f6; color: #4b5563; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    ⊘ Cancelled
                                </span>
                            @else
                                <span style="padding: 0.25rem 0.75rem; background: #f3f4f6; color: #4b5563; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Created:</span>
                            <span style="color: #2d3748;">{{ $payment->created_at->format('F d, Y h:i A') }}</span>
                        </div>
                        @if($payment->status === 'completed' && $payment->updated_at != $payment->created_at)
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                                <span style="color: #718096; font-weight: 500;">Completed:</span>
                                <span style="color: #2d3748;">{{ $payment->updated_at->format('F d, Y h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plan Information -->
                <div class="card">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">Subscription Plan</h3>
                    <div style="display: grid; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Plan Name:</span>
                            <span style="color: #2d3748; font-weight: 600;">{{ $payment->plan->name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                            <span style="color: #718096; font-weight: 500;">Monthly Price:</span>
                            <span style="color: #2d3748; font-weight: 600;">${{ number_format($payment->plan->price, 2) }}/month</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                            <span style="color: #718096; font-weight: 500;">Employee Limit:</span>
                            <span style="color: #2d3748; font-weight: 600;">
                                {{ $payment->plan->employee_limit == -1 ? 'Unlimited' : number_format($payment->plan->employee_limit) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">Quick Actions</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <a href="{{ route('client.billing') }}" class="btn btn-secondary" style="width: 100%;">
                            View All Payments
                        </a>
                        @if($payment->status === 'completed')
                            <a href="{{ route('client.dashboard') }}" class="btn btn-primary" style="width: 100%;">
                                Go to Dashboard
                            </a>
                        @endif
                        <a href="{{ route('client.upgrade-plan') }}" class="btn btn-secondary" style="width: 100%;">
                            Upgrade Plan
                        </a>
                        <a href="{{ route('contact-support') }}" class="btn btn-secondary" style="width: 100%;">
                            Contact Support
                        </a>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="card">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">Company</h3>
                    <div style="display: grid; gap: 0.75rem;">
                        <div>
                            <span style="color: #718096; font-size: 0.875rem; font-weight: 500;">Company Name:</span>
                            <p style="color: #2d3748; font-weight: 600; margin: 0.25rem 0 0 0;">{{ $payment->company->name }}</p>
                        </div>
                        <div>
                            <span style="color: #718096; font-size: 0.875rem; font-weight: 500;">Email:</span>
                            <p style="color: #2d3748; margin: 0.25rem 0 0 0;">{{ $payment->company->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        @if($payment->status === 'completed')
            <div class="card" style="margin-top: 1.5rem; background: #f0fdf4; border-left: 4px solid #10b981;">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <div style="font-size: 1.5rem;">✓</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #065f46;">Payment Successful</h4>
                        <p style="color: #047857; margin: 0; font-size: 0.875rem;">
                            Your subscription has been upgraded to the <strong>{{ $payment->plan->name }}</strong> plan. 
                            You now have access to all features included in this plan.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'pending')
            <div class="card" style="margin-top: 1.5rem; background: #fffbeb; border-left: 4px solid #f59e0b;">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <div style="font-size: 1.5rem;">⏳</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #92400e;">Payment Pending</h4>
                        <p style="color: #78350f; margin: 0; font-size: 0.875rem;">
                            Your payment is currently being processed. Please complete the payment to activate your subscription.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($payment->status === 'failed')
            <div class="card" style="margin-top: 1.5rem; background: #fef2f2; border-left: 4px solid #ef4444;">
                <div style="display: flex; align-items: start; gap: 1rem;">
                    <div style="font-size: 1.5rem;">✗</div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #991b1b;">Payment Failed</h4>
                        <p style="color: #7f1d1d; margin: 0 0 1rem 0; font-size: 0.875rem;">
                            Your payment could not be processed. Please try again or contact support if the issue persists.
                        </p>
                        <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary btn-sm">
                            Try Again
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection




