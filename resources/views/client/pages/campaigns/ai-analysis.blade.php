@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">🤖 AI Campaign Analysis</h1>
            <p style="color: #718096;">Campaign #{{ $campaign->id }} - {{ ucfirst($campaign->type) }}</p>
        </div>
        <div>
            <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">← Back to Campaign</a>
            <a href="{{ route('client.campaigns.stats', $campaign->id) }}" class="btn btn-primary" style="margin-left: 0.5rem;">View Statistics</a>
        </div>
    </div>

    {{-- Current Performance --}}
    @if(isset($analysis['current_performance']))
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">📊 Current Performance</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Total Targets</h4>
                    <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ $analysis['current_performance']['total_targets'] ?? 0 }}</p>
                </div>
                <div>
                    <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Open Rate</h4>
                    <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ number_format($analysis['current_performance']['open_rate'] ?? 0, 2) }}%</p>
                </div>
                <div>
                    <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Click Rate</h4>
                    <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin: 0;">{{ number_format($analysis['current_performance']['click_rate'] ?? 0, 2) }}%</p>
                </div>
                <div>
                    <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Submit Rate</h4>
                    <p style="font-size: 2rem; font-weight: 700; color: #e53e3e; margin: 0;">{{ number_format($analysis['current_performance']['submit_rate'] ?? 0, 2) }}%</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Risk Level --}}
    @if(isset($analysis['risk_level']))
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">⚠️ Risk Assessment</h2>
            <div style="display: flex; align-items: center; gap: 1rem;">
                @php
                    $riskColors = [
                        'high' => ['bg' => '#fed7d7', 'text' => '#c53030', 'icon' => '🔴'],
                        'medium' => ['bg' => '#fefcbf', 'text' => '#744210', 'icon' => '🟡'],
                        'low' => ['bg' => '#c6f6d5', 'text' => '#22543d', 'icon' => '🟢'],
                    ];
                    $riskLevel = $analysis['risk_level']['level'] ?? 'low';
                    $color = $riskColors[$riskLevel] ?? $riskColors['low'];
                @endphp
                <span style="font-size: 3rem;">{{ $color['icon'] }}</span>
                <div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: capitalize; color: {{ $color['text'] }};">
                        {{ $riskLevel }} Risk
                    </h3>
                    <p style="color: #718096; margin: 0;">{{ $analysis['risk_level']['description'] ?? 'Risk assessment completed' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- AI Suggestions --}}
    @if(isset($analysis['suggestions']) && count($analysis['suggestions']) > 0)
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">💡 AI Recommendations</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($analysis['suggestions'] as $suggestion)
                    @php
                        $typeColors = [
                            'critical' => ['bg' => '#fed7d7', 'text' => '#c53030', 'border' => '#fc8181'],
                            'warning' => ['bg' => '#fefcbf', 'text' => '#744210', 'border' => '#f6e05e'],
                            'info' => ['bg' => '#bee3f8', 'text' => '#2c5282', 'border' => '#90cdf4'],
                            'success' => ['bg' => '#c6f6d5', 'text' => '#22543d', 'border' => '#9ae6b4'],
                        ];
                        $type = $suggestion['type'] ?? 'info';
                        $color = $typeColors[$type] ?? $typeColors['info'];
                    @endphp
                    <div style="padding: 1rem; background-color: {{ $color['bg'] }}; border-left: 4px solid {{ $color['border'] }}; border-radius: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                            <h4 style="font-size: 1rem; font-weight: 600; color: {{ $color['text'] }}; margin: 0;">
                                {{ $suggestion['title'] ?? 'Recommendation' }}
                            </h4>
                            @if(isset($suggestion['priority']))
                                <span style="padding: 0.25rem 0.75rem; background-color: {{ $color['text'] }}; color: white; border-radius: 9999px; font-size: 0.75rem; text-transform: capitalize; font-weight: 600;">
                                    {{ $suggestion['priority'] }}
                                </span>
                            @endif
                        </div>
                        <p style="color: {{ $color['text'] }}; margin: 0;">{{ $suggestion['description'] ?? '' }}</p>
                        @if(isset($suggestion['action_required']) && $suggestion['action_required'])
                            <p style="color: {{ $color['text'] }}; margin-top: 0.5rem; margin-bottom: 0; font-weight: 600;">
                                ⚠️ Action Required
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Improvement Trend --}}
    @if(isset($analysis['improvement']))
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">📈 Improvement Trend</h2>
            <p style="font-size: 1.25rem; color: #2b6cb0; font-weight: 600;">{{ $analysis['improvement'] }}</p>
        </div>
    @endif

    {{-- Analysis Metadata --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">📋 Analysis Details</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Campaign ID</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">#{{ $analysis['campaign_id'] ?? $campaign->id }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Campaign Type</h4>
                <p style="font-size: 1.125rem; font-weight: 600; text-transform: capitalize;">{{ $analysis['campaign_type'] ?? $campaign->type }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Analysis Date</h4>
                <p style="font-size: 1.125rem; font-weight: 600;">
                    @if(isset($analysis['analysis_date']))
                        {{ \Carbon\Carbon::parse($analysis['analysis_date'])->format('M d, Y H:i') }}
                    @else
                        {{ now()->format('M d, Y H:i') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection


