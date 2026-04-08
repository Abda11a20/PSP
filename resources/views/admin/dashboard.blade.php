@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Admin Dashboard</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Companies</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ number_format($stats['total_companies']) }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ number_format($stats['total_campaigns']) }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Users</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ number_format($stats['total_users']) }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Revenue</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div class="card">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Recent Companies</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Company</th>
                            <th style="padding: 0.75rem; text-align: left;">Plan</th>
                            <th style="padding: 0.75rem; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentCompanies = \App\Models\Company::with('plan')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @foreach($recentCompanies as $recentCompany)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem;">{{ $recentCompany->name }}</td>
                            <td style="padding: 0.75rem;">{{ $recentCompany->plan->name }}</td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.75rem; background-color: #c6f6d5; color: #22543d; border-radius: 9999px; font-size: 0.875rem;">
                                    Active
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Recent Payments</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Company</th>
                            <th style="padding: 0.75rem; text-align: left;">Amount</th>
                            <th style="padding: 0.75rem; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $recentPayments = \App\Models\Payment::with('company')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @foreach($recentPayments as $payment)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem;">{{ $payment->company->name }}</td>
                            <td style="padding: 0.75rem;">${{ number_format($payment->amount, 2) }}</td>
                            <td style="padding: 0.75rem;">
                                @if($payment->status === 'completed')
                                    <span style="padding: 0.25rem 0.75rem; background-color: #c6f6d5; color: #22543d; border-radius: 9999px; font-size: 0.875rem;">
                                        Completed
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span style="padding: 0.25rem 0.75rem; background-color: #fefcbf; color: #744210; border-radius: 9999px; font-size: 0.875rem;">
                                        Pending
                                    </span>
                                @else
                                    <span style="padding: 0.25rem 0.75rem; background-color: #fed7d7; color: #742a2a; border-radius: 9999px; font-size: 0.875rem;">
                                        Failed
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">System Overview</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Active Campaigns</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ \App\Models\Campaign::where('status', 'active')->count() }}
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Emails Sent Today</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ \App\Models\Interaction::whereDate('created_at', today())->where('action_type', 'sent')->count() }}
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">New Companies (30 days)</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ \App\Models\Company::where('created_at', '>=', now()->subDays(30))->count() }}
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Monthly Revenue</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    ${{ number_format(\App\Models\Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'), 2) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
