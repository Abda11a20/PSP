@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Campaigns</h1>
            <p style="color: #718096;">Manage your phishing simulation campaigns</p>
        </div>
        <a href="{{ route('client.campaigns.create') }}" class="btn btn-primary">+ Create Campaign</a>
    </div>

    {{-- Statistics Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['total'] }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Active</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['active'] }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Draft</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['draft'] }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Completed</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['completed'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('client.campaigns.index') }}" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search campaigns..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="min-width: 150px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="form-group" style="min-width: 150px;">
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="phishing" {{ request('type') == 'phishing' ? 'selected' : '' }}>Phishing</option>
                    <option value="awareness" {{ request('type') == 'awareness' ? 'selected' : '' }}>Awareness</option>
                    <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>Training</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('client.campaigns.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Clear</a>
            </div>
        </form>
    </div>

    {{-- Campaigns Table --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">All Campaigns</h2>
        
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 0.75rem; text-align: left;">ID</th>
                        <th style="padding: 0.75rem; text-align: left;">Type</th>
                        <th style="padding: 0.75rem; text-align: left;">Status</th>
                        <th style="padding: 0.75rem; text-align: left;">Start Date</th>
                        <th style="padding: 0.75rem; text-align: left;">End Date</th>
                        <th style="padding: 0.75rem; text-align: center;">Targets</th>
                        <th style="padding: 0.75rem; text-align: center;">Interactions</th>
                        <th style="padding: 0.75rem; text-align: left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        @php
                            // Calculate sequential number for this user's campaigns
                            // Use loop index + pagination offset to get the correct sequential number
                            $campaignNumber = $loop->iteration + (($campaigns->currentPage() - 1) * $campaigns->perPage());
                        @endphp
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem;">#{{ $campaignNumber }}</td>
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
                            <td style="padding: 0.75rem;">{{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'N/A' }}</td>
                            <td style="padding: 0.75rem;">{{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'N/A' }}</td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <span style="font-weight: 600;">{{ $campaign->targets->count() }}</span>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <span style="font-weight: 600;">{{ $campaign->interactions->count() }}</span>
                            </td>
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">View</a>
                                    @if($campaign->status === 'draft')
                                        <a href="{{ route('client.campaigns.edit', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Edit</a>
                                        <a href="{{ route('client.campaigns.add-targets', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Add Targets</a>
                                    @endif
                                    <a href="{{ route('client.campaigns.stats', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Stats</a>
                                    <a href="{{ route('client.campaigns.ai-analysis', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">AI</a>
                                    <a href="{{ route('client.campaigns.report', $campaign->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Report</a>
                                    @if(!in_array($campaign->status, ['active', 'running']))
                                        <form method="POST" action="{{ route('client.campaigns.destroy', $campaign->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 2rem; text-align: center; color: #718096;">
                                No campaigns found. <a href="{{ route('client.campaigns.create') }}" style="color: #2b6cb0;">Create your first campaign</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($campaigns->hasPages())
            <div style="margin-top: 2rem;">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

