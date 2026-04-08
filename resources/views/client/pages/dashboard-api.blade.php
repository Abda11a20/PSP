@extends('layouts.app')

@php
    // Generate JWT token for API calls
    $user = Auth::guard('company')->user();
    $jwtToken = '';
    if ($user) {
        try {
            $jwtToken = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        } catch (\Exception $e) {
            // Fallback if JWT fails
            $jwtToken = '';
        }
    }
@endphp

@push('styles')
<meta name="api-token" content="{{ $jwtToken }}">
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700;">Dashboard API View</h1>
        <div>
            <button onclick="refreshAll()" class="btn btn-primary">🔄 Refresh All</button>
            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">← Back to Dashboard</a>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div id="loadingIndicator" style="display: none; text-align: center; padding: 2rem;">
        <div style="font-size: 1.5rem;">Loading...</div>
    </div>

    {{-- Dashboard Overview --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">📊 Dashboard Overview</h2>
        <button onclick="loadDashboard()" class="btn btn-primary btn-sm">Load Dashboard Data</button>
        <div id="dashboardData" style="margin-top: 1rem;"></div>
    </div>

    {{-- Statistics --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">📈 Statistics</h2>
        <button onclick="loadStats()" class="btn btn-primary btn-sm">Load Statistics</button>
        <div id="statsData" style="margin-top: 1rem;"></div>
    </div>

    {{-- Analytics --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">📊 Analytics</h2>
        <button onclick="loadAnalytics()" class="btn btn-primary btn-sm">Load Analytics</button>
        <div id="analyticsData" style="margin-top: 1rem;"></div>
    </div>

    {{-- Recent Activity --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">🕐 Recent Activity</h2>
        <button onclick="loadActivity()" class="btn btn-primary btn-sm">Load Recent Activity</button>
        <div id="activityData" style="margin-top: 1rem;"></div>
    </div>

    {{-- Performance --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">⚡ Performance</h2>
        <button onclick="loadPerformance()" class="btn btn-primary btn-sm">Load Performance</button>
        <div id="performanceData" style="margin-top: 1rem;"></div>
    </div>

    {{-- Charts --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">📊 Charts Data</h2>
        <button onclick="loadCharts()" class="btn btn-primary btn-sm">Load Charts Data</button>
        <div id="chartsData" style="margin-top: 1rem;">
            <canvas id="campaignsChart" style="max-height: 300px; margin-bottom: 2rem;"></canvas>
            <canvas id="interactionsChart" style="max-height: 300px; margin-bottom: 2rem;"></canvas>
            <canvas id="statusChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    const API_BASE = '/api/dashboard';
    // Get token from meta tag
    const tokenMeta = document.querySelector('meta[name="api-token"]');
    const token = tokenMeta ? 'Bearer ' + tokenMeta.getAttribute('content') : '';

    function showLoading() {
        document.getElementById('loadingIndicator').style.display = 'block';
    }

    function hideLoading() {
        document.getElementById('loadingIndicator').style.display = 'none';
    }

    function displayData(elementId, data, format = 'json') {
        const element = document.getElementById(elementId);
        if (format === 'json') {
            element.innerHTML = '<pre style="background: #f7fafc; padding: 1rem; border-radius: 0.5rem; overflow-x: auto;">' + JSON.stringify(data, null, 2) + '</pre>';
        } else {
            element.innerHTML = data;
        }
    }

    async function apiCall(endpoint) {
        try {
            showLoading();
            const response = await fetch(API_BASE + endpoint, {
                headers: {
                    'Authorization': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            hideLoading();
            return data;
        } catch (error) {
            hideLoading();
            console.error('API Error:', error);
            return { error: error.message };
        }
    }

    async function loadDashboard() {
        const data = await apiCall('/');
        displayData('dashboardData', data);
    }

    async function loadStats() {
        const data = await apiCall('/stats');
        displayData('statsData', data);
    }

    async function loadAnalytics() {
        const data = await apiCall('/analytics');
        displayData('analyticsData', data);
    }

    async function loadActivity() {
        const data = await apiCall('/recent-activity');
        if (data.data && Array.isArray(data.data)) {
            let html = '<div style="display: flex; flex-direction: column; gap: 1rem;">';
            data.data.forEach(activity => {
                html += `
                    <div style="padding: 1rem; background: #f7fafc; border-radius: 0.5rem; border-left: 4px solid #2b6cb0;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 1.5rem;">${activity.icon || '📊'}</span>
                            <div>
                                <div style="font-weight: 600;">${activity.title || activity.message}</div>
                                <div style="font-size: 0.875rem; color: #718096;">${new Date(activity.timestamp).toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            displayData('activityData', html, 'html');
        } else {
            displayData('activityData', data);
        }
    }

    async function loadPerformance() {
        const data = await apiCall('/performance');
        if (data.data && Array.isArray(data.data)) {
            let html = '<div style="overflow-x: auto;"><table style="width: 100%; border-collapse: collapse;"><thead><tr style="border-bottom: 2px solid #e2e8f0;"><th style="padding: 0.75rem; text-align: left;">Campaign</th><th style="padding: 0.75rem; text-align: left;">Type</th><th style="padding: 0.75rem; text-align: center;">Open Rate</th><th style="padding: 0.75rem; text-align: center;">Click Rate</th><th style="padding: 0.75rem; text-align: center;">Submit Rate</th></tr></thead><tbody>';
            data.data.forEach(perf => {
                html += `
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 0.75rem;">#${perf.campaign_id}</td>
                        <td style="padding: 0.75rem; text-transform: capitalize;">${perf.type}</td>
                        <td style="padding: 0.75rem; text-align: center;">${perf.open_rate}%</td>
                        <td style="padding: 0.75rem; text-align: center;">${perf.click_rate}%</td>
                        <td style="padding: 0.75rem; text-align: center;">${perf.submit_rate}%</td>
                    </tr>
                `;
            });
            html += '</tbody></table></div>';
            displayData('performanceData', html, 'html');
        } else {
            displayData('performanceData', data);
        }
    }

    async function loadCharts() {
        const data = await apiCall('/charts');
        displayData('chartsData', data);
        
        if (data.data) {
            // Campaigns Over Time Chart
            if (data.data.campaigns_over_time) {
                const ctx1 = document.getElementById('campaignsChart').getContext('2d');
                new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: data.data.campaigns_over_time.map(d => d.month),
                        datasets: [{
                            label: 'Campaigns',
                            data: data.data.campaigns_over_time.map(d => d.count),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Campaigns Over Time'
                            }
                        }
                    }
                });
            }

            // Interactions by Type Chart
            if (data.data.interactions_by_type) {
                const ctx2 = document.getElementById('interactionsChart').getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data.data.interactions_by_type),
                        datasets: [{
                            label: 'Interactions',
                            data: Object.values(data.data.interactions_by_type),
                            backgroundColor: ['#4facfe', '#43e97b', '#fa709a', '#f093fb']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Interactions by Type'
                            }
                        }
                    }
                });
            }

            // Status Distribution Chart
            if (data.data.status_distribution) {
                const ctx3 = document.getElementById('statusChart').getContext('2d');
                new Chart(ctx3, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(data.data.status_distribution),
                        datasets: [{
                            data: Object.values(data.data.status_distribution),
                            backgroundColor: ['#c6f6d5', '#bee3f8', '#fefcbf', '#fed7d7']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Campaign Status Distribution'
                            }
                        }
                    }
                });
            }
        }
    }

    function refreshAll() {
        loadDashboard();
        loadStats();
        loadAnalytics();
        loadActivity();
        loadPerformance();
        loadCharts();
    }

    // Auto-load on page load
    window.addEventListener('DOMContentLoaded', function() {
        loadDashboard();
    });
</script>
@endpush
@endsection

