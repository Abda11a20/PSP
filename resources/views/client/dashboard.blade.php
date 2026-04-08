@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">
        Welcome, {{ $company->name }}
    </h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_campaigns'] }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Active Campaigns</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['active_campaigns'] }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Targets</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_targets'] }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Interactions</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_interactions'] }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; margin: 0;">Recent Campaigns</h2>
                <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary btn-sm">
                    + Create New Campaign
                </a>
            </div>
            
            @if($recentCampaigns->count() > 0)
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 0.75rem; text-align: left;">ID</th>
                                <th style="padding: 0.75rem; text-align: left;">Campaign Type</th>
                                <th style="padding: 0.75rem; text-align: left;">Status</th>
                                <th style="padding: 0.75rem; text-align: left;">Start Date</th>
                                <th style="padding: 0.75rem; text-align: left;">End Date</th>
                                <th style="padding: 0.75rem; text-align: center;">Targets</th>
                                <th style="padding: 0.75rem; text-align: left;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCampaigns as $campaign)
                            @php
                                // Calculate sequential number for this user's campaigns
                                // Get all campaign IDs for this company ordered by creation date
                                $allCampaignIds = $company->campaigns()->orderBy('created_at', 'desc')->pluck('id')->toArray();
                                $campaignNumber = array_search($campaign->id, $allCampaignIds) + 1;
                            @endphp
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">
                                    <span style="font-weight: 600; color: #718096;">#{{ $campaignNumber }}</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <span style="text-transform: capitalize; font-weight: 600;">{{ $campaign->type }}</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    @php
                                        $statusColors = [
                                            'active' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                            'running' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                            'draft' => ['bg' => '#e2e8f0', 'text' => '#4a5568'],
                                            'completed' => ['bg' => '#bee3f8', 'text' => '#2c5282'],
                                            'paused' => ['bg' => '#fefcbf', 'text' => '#744210'],
                                        ];
                                        $color = $statusColors[$campaign->status] ?? ['bg' => '#e2e8f0', 'text' => '#4a5568'];
                                    @endphp
                                    <span style="padding: 0.25rem 0.75rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                                        {{ $campaign->status }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    {{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'Not set' }}
                                </td>
                                <td style="padding: 0.75rem;">
                                    {{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Not set' }}
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span style="font-weight: 600;">{{ $campaign->targets->count() }}</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">View</a>
                                        <a href="{{ route('client.campaigns.report', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Report</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem; text-align: center;">
                    <a href="{{ route('client.campaigns.index') }}" class="btn btn-secondary btn-sm">View All Campaigns</a>
                </div>
            @else
                {{-- Empty State - No Campaigns --}}
                <div style="text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1rem; color: white;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📧</div>
                    <h3 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; color: white;">
                        @if($stats['total_campaigns'] == 0)
                            Create Your First Campaign
                        @else
                            No Recent Campaigns
                        @endif
                    </h3>
                    <p style="font-size: 1.125rem; margin-bottom: 2rem; opacity: 0.9; max-width: 500px; margin-left: auto; margin-right: auto;">
                        @if($stats['total_campaigns'] == 0)
                            Start protecting your organization by creating your first phishing simulation campaign. It's quick and easy!
                        @else
                            Create a new campaign to continue improving your organization's security posture.
                        @endif
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary" style="background-color: white; color: #667eea; padding: 0.75rem 2rem; font-size: 1.125rem; font-weight: 600; border: none; border-radius: 0.5rem; text-decoration: none; display: inline-block;">
                            @if($stats['total_campaigns'] == 0)
                                ✨ Create Your First Campaign
                            @else
                                ➕ Create New Campaign
                            @endif
                        </a>
                        @if($stats['total_campaigns'] == 0)
                            <a href="{{ route('client.templates') }}" class="btn btn-secondary" style="background-color: rgba(255, 255, 255, 0.2); color: white; padding: 0.75rem 2rem; font-size: 1.125rem; font-weight: 600; border: 2px solid white; border-radius: 0.5rem; text-decoration: none; display: inline-block;">
                                📋 Browse Templates
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Current Plan</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #2b6cb0; margin-bottom: 0.5rem;">
                    {{ $company->plan->name }}
                </p>
                <p style="color: #718096;">
                    ${{ number_format($company->plan->price, 2) }}/month
                </p>
                <p style="color: #718096; margin-top: 1rem;">
                    Employee Limit: {{ $company->plan->employee_limit == -1 ? 'Unlimited' : $company->plan->employee_limit }}
                </p>
                <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
                    <a href="{{ route('client.upgrade-plan') }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                        Upgrade Plan
                    </a>
                    <a href="{{ route('client.billing') }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                        View Billing
                    </a>
                </div>
            </div>

            <div class="card">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary">
                        Create Campaign
                    </a>
                    <a href="{{ route('client.templates') }}" class="btn btn-secondary">
                        View Templates
                    </a>
                    <a href="{{ route('client.users') }}" class="btn btn-secondary">
                        Manage Users
                    </a>
                    <a href="{{ route('client.reports') }}" class="btn btn-secondary">
                        View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Performance Overview</h2>
        @php
            $openRate = $stats['total_interactions'] > 0 
                ? (\App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->where('action_type', 'opened')->count() / $stats['total_interactions']) * 100 
                : 0;
            
            $clickRate = $stats['total_interactions'] > 0 
                ? (\App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->where('action_type', 'clicked')->count() / $stats['total_interactions']) * 100 
                : 0;
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Average Open Rate</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ number_format($openRate, 1) }}%
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Average Click Rate</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ number_format($clickRate, 1) }}%
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Emails Sent</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ \App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
                        $q->where('company_id', $company->id);
                    })->where('action_type', 'sent')->count() }}
                </p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem;">Users Trained</h4>
                <p style="font-size: 1.5rem; font-weight: 600;">
                    {{ \App\Models\CampaignTarget::whereHas('campaign', function($q) use ($company) {
                        $q->where('company_id', $company->id)->where('type', 'training');
                    })->count() }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
