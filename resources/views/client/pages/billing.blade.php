@extends('layouts.app')

@section('content')
<div class="container">
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Billing & Payments</h1>
                <p style="color: #718096;">Manage your subscription and view payment history</p>
            </div>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary">
                    Upgrade Plan
                </a>
                <a href="{{ route('contact-support') }}" class="btn btn-secondary">
                    Contact Support
                </a>
            </div>
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

    <!-- Current Subscription Card -->
    <div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Current Subscription</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <p style="opacity: 0.9; margin-bottom: 0.5rem; font-size: 0.875rem;">Plan Name</p>
                <p style="font-size: 1.75rem; font-weight: 700; margin: 0;">{{ $company->plan->name }}</p>
            </div>
            <div>
                <p style="opacity: 0.9; margin-bottom: 0.5rem; font-size: 0.875rem;">Monthly Price</p>
                <p style="font-size: 1.75rem; font-weight: 700; margin: 0;">${{ number_format($company->plan->price, 2) }}</p>
            </div>
            <div>
                <p style="opacity: 0.9; margin-bottom: 0.5rem; font-size: 0.875rem;">Employee Limit</p>
                <p style="font-size: 1.75rem; font-weight: 700; margin: 0;">
                    {{ $company->plan->employee_limit == -1 ? 'Unlimited' : number_format($company->plan->employee_limit) }}
                </p>
            </div>
            <div>
                <p style="opacity: 0.9; margin-bottom: 0.5rem; font-size: 0.875rem;">Status</p>
                <div style="display: inline-block; padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.2); border-radius: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.3);">
                    <span style="font-weight: 600;">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Spent</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">${{ number_format($stats['total_amount'], 2) }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Payments</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['total_payments'] }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">This Month</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">${{ number_format($stats['monthly_spending'], 2) }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Completed</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['completed_payments'] }}</p>
        </div>
    </div>

    <!-- Payment History -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h2 style="font-size: 1.5rem; margin: 0;">Payment History</h2>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <span style="padding: 0.5rem 1rem; background: #e6fffa; color: #047857; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600;">
                    Completed: {{ $stats['completed_payments'] }}
                </span>
                @if($stats['pending_payments'] > 0)
                    <span style="padding: 0.5rem 1rem; background: #fef3c7; color: #92400e; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600;">
                        Pending: {{ $stats['pending_payments'] }}
                    </span>
                @endif
                @if($stats['failed_payments'] > 0)
                    <span style="padding: 0.5rem 1rem; background: #fee2e2; color: #991b1b; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600;">
                        Failed: {{ $stats['failed_payments'] }}
                    </span>
                @endif
            </div>
        </div>

        @if($payments->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; background-color: #f7fafc;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #4a5568;">Date</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #4a5568;">Transaction ID</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; color: #4a5568;">Plan</th>
                            <th style="padding: 0.75rem; text-align: right; font-weight: 600; color: #4a5568;">Amount</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #4a5568;">Status</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600; color: #4a5568;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem; color: #4a5568;">
                                    {{ $payment->created_at->format('M d, Y') }}<br>
                                    <small style="color: #718096;">{{ $payment->created_at->format('h:i A') }}</small>
                                </td>
                                <td style="padding: 0.75rem; color: #4a5568;">
                                    <code style="background: #f7fafc; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;">
                                        {{ $payment->transaction_id ?? 'N/A' }}
                                    </code>
                                </td>
                                <td style="padding: 0.75rem; color: #4a5568;">
                                    <strong>{{ $payment->plan->name }}</strong><br>
                                    <small style="color: #718096;">
                                        {{ $payment->plan->employee_limit == -1 ? 'Unlimited' : number_format($payment->plan->employee_limit) }} employees
                                    </small>
                                </td>
                                <td style="padding: 0.75rem; text-align: right; color: #4a5568; font-weight: 600;">
                                    ${{ number_format($payment->amount, 2) }}
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
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
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    @if($payment->status === 'pending')
                                        <a href="{{ route('checkout', $payment->transaction_id) }}" 
                                           class="btn btn-primary btn-sm" 
                                           style="margin-right: 0.5rem;">
                                            Complete Payment
                                        </a>
                                        <form action="{{ route('payment.cancel', $payment->transaction_id) }}" 
                                              method="POST" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to cancel this payment?');">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                Cancel
                                            </button>
                                        </form>
                                    @elseif($payment->status === 'completed')
                                        <a href="{{ route('payment.status', $payment->transaction_id) }}" 
                                           class="btn btn-secondary btn-sm">
                                            View Details
                                        </a>
                                    @else
                                        <a href="{{ route('payment.status', $payment->transaction_id) }}" 
                                           class="btn btn-secondary btn-sm">
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem;">
                {{ $payments->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No payment history yet.</p>
                <p style="margin-bottom: 1.5rem;">Start by upgrading your plan to see your payment history here.</p>
                <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary">
                    Upgrade Plan
                </a>
            </div>
        @endif
    </div>

    <!-- Additional Information -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <div class="card">
            <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">💡 Need Help?</h3>
            <p style="color: #718096; margin-bottom: 1rem;">
                Have questions about your billing or payments? Our support team is here to help.
            </p>
            <a href="{{ route('contact-support') }}" class="btn btn-secondary">
                Contact Support
            </a>
        </div>

        <div class="card">
            <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">📈 Upgrade Your Plan</h3>
            <p style="color: #718096; margin-bottom: 1rem;">
                Unlock more features and increase your employee limit by upgrading your subscription.
            </p>
            <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary">
                View Plans
            </a>
        </div>
    </div>
</div>
@endsection




