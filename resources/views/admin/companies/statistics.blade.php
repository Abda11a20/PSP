@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Company Statistics</h1>
            <p style="color: #718096;">{{ $company->name }} - Comprehensive Analytics</p>
        </div>
        <div>
            <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-secondary">← Back to Company</a>
        </div>
    </div>

    {{-- Overview Statistics --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalCampaigns }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Targets</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalTargets }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Interactions</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalInteractions }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Vulnerability Rate</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $averageVulnerabilityRate }}%</p>
        </div>
    </div>

    {{-- Interaction Breakdown --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Emails Opened</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ $totalOpens }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Links Clicked</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ $totalClicks }}</p>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Forms Submitted</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #e53e3e; margin: 0;">{{ $totalSubmits }}</p>
        </div>
    </div>

    {{-- Campaign Performance --}}
    @if($campaignPerformance->count() > 0)
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Performance</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Campaign ID</th>
                            <th style="padding: 0.75rem; text-align: left;">Type</th>
                            <th style="padding: 0.75rem; text-align: left;">Status</th>
                            <th style="padding: 0.75rem; text-align: center;">Targets</th>
                            <th style="padding: 0.75rem; text-align: center;">Interactions</th>
                            <th style="padding: 0.75rem; text-align: center;">Vulnerability Rate</th>
                            <th style="padding: 0.75rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaignPerformance as $perf)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">#{{ $perf['campaign_id'] }}</td>
                                <td style="padding: 0.75rem; text-transform: capitalize;">{{ $perf['type'] }}</td>
                                <td style="padding: 0.75rem;">
                                    @php
                                        $statusColors = [
                                            'active' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                            'running' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                            'draft' => ['bg' => '#e2e8f0', 'text' => '#4a5568'],
                                            'completed' => ['bg' => '#bee3f8', 'text' => '#2c5282'],
                                            'paused' => ['bg' => '#fefcbf', 'text' => '#744210'],
                                        ];
                                        $color = $statusColors[$perf['status']] ?? ['bg' => '#e2e8f0', 'text' => '#4a5568'];
                                    @endphp
                                    <span style="padding: 0.25rem 0.75rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize;">
                                        {{ $perf['status'] }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem; text-align: center; font-weight: 600;">{{ $perf['targets_count'] }}</td>
                                <td style="padding: 0.75rem; text-align: center; font-weight: 600;">{{ $perf['interactions_count'] }}</td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span style="padding: 0.25rem 0.75rem; background-color: {{ $perf['vulnerability_rate'] > 20 ? '#fed7d7' : ($perf['vulnerability_rate'] > 10 ? '#fefcbf' : '#c6f6d5') }}; color: {{ $perf['vulnerability_rate'] > 20 ? '#c53030' : ($perf['vulnerability_rate'] > 10 ? '#744210' : '#22543d') }}; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                        {{ $perf['vulnerability_rate'] }}%
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="/dashboard/campaigns/{{ $perf['campaign_id'] }}" class="btn btn-primary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="margin-bottom: 2rem;">
            <div style="text-align: center; padding: 2rem; color: #718096;">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No campaigns found for this company.</p>
            </div>
        </div>
    @endif

    {{-- Company Information --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Company Information</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Company Name</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">{{ $company->name }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Email</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">{{ $company->email }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Subscription Plan</h4>
                @if($company->plan)
                    <p style="font-size: 1.125rem; font-weight: 600;">{{ $company->plan->name }} - ${{ number_format($company->plan->price, 2) }}/month</p>
                    <p style="font-size: 0.875rem; color: #718096;">Employee Limit: {{ $company->plan->employee_limit }}</p>
                @else
                    <p style="font-size: 1.125rem; color: #718096;">No Plan Assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


