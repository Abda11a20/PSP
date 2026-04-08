@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Campaign Statistics</h1>
            <p style="color: #718096;">Campaign #{{ $campaign->id }} - {{ ucfirst($campaign->type) }}</p>
        </div>
        <div>
            <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">← Back to Campaign</a>
            <a href="{{ route('client.campaigns.ai-analysis', $campaign->id) }}" class="btn btn-primary" style="margin-left: 0.5rem;">AI Analysis</a>
        </div>
    </div>

    {{-- Overview Statistics --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Targets</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_targets'] ?? 0 }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Emails Sent</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_sent'] ?? 0 }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Opened</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_opened'] ?? 0 }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Clicked</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_clicked'] ?? 0 }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Submitted</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $stats['total_submitted'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Rates --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Open Rate</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ number_format($stats['open_rate'] ?? 0, 2) }}%</p>
            <p style="font-size: 0.875rem; color: #718096; margin-top: 0.5rem; margin-bottom: 0;">
                {{ $stats['total_opened'] ?? 0 }} of {{ $stats['total_sent'] ?? 0 }} emails
            </p>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Click Rate</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ number_format($stats['click_rate'] ?? 0, 2) }}%</p>
            <p style="font-size: 0.875rem; color: #718096; margin-top: 0.5rem; margin-bottom: 0;">
                {{ $stats['total_clicked'] ?? 0 }} of {{ $stats['total_opened'] ?? 0 }} opened
            </p>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Submit Rate</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #e53e3e; margin: 0;">{{ number_format($stats['submit_rate'] ?? 0, 2) }}%</p>
            <p style="font-size: 0.875rem; color: #718096; margin-top: 0.5rem; margin-bottom: 0;">
                {{ $stats['total_submitted'] ?? 0 }} of {{ $stats['total_clicked'] ?? 0 }} clicked
            </p>
        </div>
        <div class="card">
            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; color: #4a5568;">Vulnerability Rate</h3>
            <p style="font-size: 2rem; font-weight: 700; color: #e53e3e; margin: 0;">{{ number_format($stats['vulnerability_rate'] ?? 0, 2) }}%</p>
            <p style="font-size: 0.875rem; color: #718096; margin-top: 0.5rem; margin-bottom: 0;">
                Employees who submitted credentials
            </p>
        </div>
    </div>

    {{-- Vulnerable Employees --}}
    @if(isset($stats['vulnerable_employees']) && count($stats['vulnerable_employees']) > 0)
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">⚠️ Vulnerable Employees ({{ count($stats['vulnerable_employees']) }})</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Name</th>
                            <th style="padding: 0.75rem; text-align: left;">Email</th>
                            <th style="padding: 0.75rem; text-align: left;">Risk Level</th>
                            <th style="padding: 0.75rem; text-align: left;">Actions Taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['vulnerable_employees'] as $employee)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">{{ $employee['name'] ?? 'N/A' }}</td>
                                <td style="padding: 0.75rem;">{{ $employee['email'] ?? 'N/A' }}</td>
                                <td style="padding: 0.75rem;">
                                    @php
                                        $riskColors = [
                                            'high' => ['bg' => '#fed7d7', 'text' => '#c53030'],
                                            'medium' => ['bg' => '#fefcbf', 'text' => '#744210'],
                                            'low' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                        ];
                                        $color = $riskColors[$employee['risk_level'] ?? 'low'] ?? $riskColors['low'];
                                    @endphp
                                    <span style="padding: 0.25rem 0.75rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                                        {{ $employee['risk_level'] ?? 'low' }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    @if(isset($employee['actions']) && is_array($employee['actions']))
                                        {{ implode(', ', $employee['actions']) }}
                                    @else
                                        Submitted credentials
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Campaign Information --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Information</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Campaign Type</h4>
                <p style="font-size: 1.125rem; font-weight: 600; text-transform: capitalize;">{{ $campaign->type }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Status</h4>
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
                <span style="padding: 0.5rem 1rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                    {{ $campaign->status }}
                </span>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Start Date</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">{{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'Not set' }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">End Date</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">{{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Not set' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection


