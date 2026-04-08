@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Campaign Report</h1>
            <p style="color: #718096; font-size: 1rem;">
                Campaign #{{ $campaign->id }} - {{ ucfirst($campaign->type) }} Campaign
            </p>
        </div>
        <div>
            <a href="{{ route('client.reports') }}" class="btn btn-secondary">← Back to Reports</a>
            <button onclick="window.print()" class="btn btn-primary" style="margin-left: 0.5rem;">📄 Print Report</button>
        </div>
    </div>

    {{-- Campaign Info Card --}}
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Campaign Type</h4>
                <p style="font-size: 1.25rem; font-weight: 600; text-transform: capitalize;">{{ $campaign->type }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Status</h4>
                <span style="padding: 0.5rem 1rem; background-color: 
                    @if($campaign->status === 'active') #c6f6d5; color: #22543d;
                    @elseif($campaign->status === 'completed') #bee3f8; color: #2c5282;
                    @elseif($campaign->status === 'paused') #fefcbf; color: #744210;
                    @else #e2e8f0; color: #4a5568;
                    @endif
                    border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                    {{ $campaign->status }}
                </span>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Start Date</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $campaign->start_date->format('M d, Y') }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">End Date</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Ongoing' }}</p>
            </div>
        </div>
    </div>

    {{-- Summary Statistics --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Targets</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalTargets }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Emails Sent</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalSent }}</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Open Rate</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $openedPercentage }}%</p>
            <p style="font-size: 0.875rem; opacity: 0.8; margin-top: 0.25rem;">{{ $totalOpened }} opened</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Click Rate</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $clickedPercentage }}%</p>
            <p style="font-size: 0.875rem; opacity: 0.8; margin-top: 0.25rem;">{{ $totalClicked }} clicked</p>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Submit Rate</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $submittedPercentage }}%</p>
            <p style="font-size: 0.875rem; opacity: 0.8; margin-top: 0.25rem;">{{ $totalSubmitted }} submitted</p>
        </div>
    </div>

    {{-- Conversion Funnel --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Conversion Funnel</h2>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @php
                $stages = [
                    ['name' => 'Sent', 'count' => $totalSent, 'percentage' => 100, 'color' => '#667eea'],
                    ['name' => 'Opened', 'count' => $totalOpened, 'percentage' => $openedPercentage, 'color' => '#4facfe'],
                    ['name' => 'Clicked', 'count' => $totalClicked, 'percentage' => $clickedPercentage, 'color' => '#43e97b'],
                    ['name' => 'Submitted', 'count' => $totalSubmitted, 'percentage' => $submittedPercentage, 'color' => '#fa709a'],
                ];
            @endphp
            @foreach($stages as $stage)
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600;">{{ $stage['name'] }}</span>
                        <span style="color: #718096;">{{ $stage['count'] }} ({{ $stage['percentage'] }}%)</span>
                    </div>
                    <div style="width: 100%; height: 30px; background-color: #e2e8f0; border-radius: 9999px; overflow: hidden;">
                        <div style="width: {{ $stage['percentage'] }}%; height: 100%; background: linear-gradient(135deg, {{ $stage['color'] }} 0%, {{ $stage['color'] }}dd 100%); transition: width 0.5s ease;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Target Analytics Table --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Target Analytics</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.75rem; text-align: left;">Name</th>
                        <th style="padding: 0.75rem; text-align: left;">Email</th>
                        <th style="padding: 0.75rem; text-align: center;">Sent</th>
                        <th style="padding: 0.75rem; text-align: center;">Opened</th>
                        <th style="padding: 0.75rem; text-align: center;">Clicked</th>
                        <th style="padding: 0.75rem; text-align: center;">Submitted</th>
                        <th style="padding: 0.75rem; text-align: center;">Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targetAnalytics as $target)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem;">{{ $target['name'] }}</td>
                            <td style="padding: 0.75rem;">{{ $target['email'] }}</td>
                            <td style="padding: 0.75rem; text-align: center;">{{ $target['sent'] }}</td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($target['opened'] > 0)
                                    <span style="color: #2b6cb0;">✓ {{ $target['opened'] }}</span>
                                @else
                                    <span style="color: #cbd5e0;">—</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($target['clicked'] > 0)
                                    <span style="color: #38a169;">✓ {{ $target['clicked'] }}</span>
                                @else
                                    <span style="color: #cbd5e0;">—</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @if($target['submitted'] > 0)
                                    <span style="color: #e53e3e;">⚠ {{ $target['submitted'] }}</span>
                                @else
                                    <span style="color: #cbd5e0;">—</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                @php
                                    $riskColors = [
                                        'high' => ['bg' => '#fed7d7', 'text' => '#742a2a'],
                                        'medium' => ['bg' => '#feebc8', 'text' => '#744210'],
                                        'low' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                                        'none' => ['bg' => '#e2e8f0', 'text' => '#4a5568'],
                                    ];
                                    $risk = $riskColors[$target['risk_level']] ?? $riskColors['none'];
                                @endphp
                                <span style="padding: 0.25rem 0.75rem; background-color: {{ $risk['bg'] }}; color: {{ $risk['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                                    {{ $target['risk_level'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 2rem; text-align: center; color: #718096;">No targets found for this campaign.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Timeline Chart --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Activity Timeline</h2>
        <div style="overflow-x: auto;">
            <canvas id="timelineChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    {{-- Hourly Distribution --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Hourly Activity Distribution</h2>
        <div style="overflow-x: auto;">
            <canvas id="hourlyChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    {{-- Action Summary --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Action Summary</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
            <div style="text-align: center; padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #2b6cb0;">{{ $totalSent }}</div>
                <div style="color: #718096; margin-top: 0.5rem;">Emails Sent</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #38a169;">{{ $totalOpened }}</div>
                <div style="color: #718096; margin-top: 0.5rem;">Emails Opened</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #d69e2e;">{{ $totalClicked }}</div>
                <div style="color: #718096; margin-top: 0.5rem;">Links Clicked</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #e53e3e;">{{ $totalSubmitted }}</div>
                <div style="color: #718096; margin-top: 0.5rem;">Forms Submitted</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #718096;">{{ $totalFailed }}</div>
                <div style="color: #718096; margin-top: 0.5rem;">Failed</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Timeline Chart
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    const timelineData = @json($timelineData);
    
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: timelineData.map(d => new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })),
            datasets: [
                {
                    label: 'Opened',
                    data: timelineData.map(d => d.opened),
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Clicked',
                    data: timelineData.map(d => d.clicked),
                    borderColor: '#43e97b',
                    backgroundColor: 'rgba(67, 233, 123, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Submitted',
                    data: timelineData.map(d => d.submitted),
                    borderColor: '#fa709a',
                    backgroundColor: 'rgba(250, 112, 154, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    const hourlyData = @json($hourlyData);
    
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: hourlyData.map(d => d.hour + ':00'),
            datasets: [
                {
                    label: 'Opened',
                    data: hourlyData.map(d => d.opened),
                    backgroundColor: 'rgba(79, 172, 254, 0.6)',
                },
                {
                    label: 'Clicked',
                    data: hourlyData.map(d => d.clicked),
                    backgroundColor: 'rgba(67, 233, 123, 0.6)',
                },
                {
                    label: 'Submitted',
                    data: hourlyData.map(d => d.submitted),
                    backgroundColor: 'rgba(250, 112, 154, 0.6)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        .btn, button {
            display: none;
        }
        .card {
            page-break-inside: avoid;
        }
    }
</style>
@endpush
@endsection


