@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Company Details</h1>
            <p style="color: #718096;">Company #{{ $company->id }} - {{ $company->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">← Back to Companies</a>
            <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-primary" style="margin-left: 0.5rem;">Edit</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Company Info --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Company Information</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Company Name</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $company->name }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Email Address</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $company->email }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Subscription Plan</h4>
                @if($company->plan)
                    <span style="padding: 0.5rem 1rem; background-color: #bee3f8; color: #2c5282; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                        {{ $company->plan->name }} - ${{ number_format($company->plan->price, 2) }}/month
                    </span>
                @else
                    <span style="color: #718096;">No Plan Assigned</span>
                @endif
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Role</h4>
                <span style="padding: 0.5rem 1rem; background-color: {{ $company->role === 'admin' ? '#fefcbf' : '#e2e8f0' }}; color: {{ $company->role === 'admin' ? '#744210' : '#4a5568' }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                    {{ $company->role ?? 'client' }}
                </span>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Created</h4>
                <p style="font-size: 1rem; color: #718096;">{{ $company->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Last Updated</h4>
                <p style="font-size: 1rem; color: #718096;">{{ $company->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $campaignsCount }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Users</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $usersCount }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Payments</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $paymentsCount }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Spent</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">${{ number_format($totalSpent, 2) }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Quick Actions</h2>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-primary">Edit Company</a>
            <a href="{{ route('admin.companies.statistics', $company->id) }}" class="btn btn-secondary">View Statistics</a>
            @if($company->id !== $currentUser->id)
                <form method="POST" action="{{ route('admin.companies.destroy', $company->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this company? This will permanently delete all associated data.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Company</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Recent Campaigns --}}
    @if($recentCampaigns->count() > 0)
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Recent Campaigns</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">ID</th>
                            <th style="padding: 0.75rem; text-align: left;">Type</th>
                            <th style="padding: 0.75rem; text-align: left;">Status</th>
                            <th style="padding: 0.75rem; text-align: left;">Start Date</th>
                            <th style="padding: 0.75rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCampaigns as $campaign)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">#{{ $campaign->id }}</td>
                                <td style="padding: 0.75rem; text-transform: capitalize;">{{ $campaign->type }}</td>
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
                                    <span style="padding: 0.25rem 0.75rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize;">
                                        {{ $campaign->status }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">{{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'N/A' }}</td>
                                <td style="padding: 0.75rem;">
                                    <a href="/dashboard/campaigns/{{ $campaign->id }}" class="btn btn-primary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection


