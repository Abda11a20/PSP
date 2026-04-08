@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">Reports</h1>
        <p style="color:#718096; margin-bottom:1rem;">Select a campaign to view its detailed analytics report.</p>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="padding:.75rem;text-align:left;">Campaign</th>
                        <th style="padding:.75rem;text-align:left;">Type</th>
                        <th style="padding:.75rem;text-align:left;">Status</th>
                        <th style="padding:.75rem;text-align:left;">Period</th>
                        <th style="padding:.75rem;text-align:left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:.75rem;">Campaign #{{ $campaign->id }}</td>
                            <td style="padding:.75rem; text-transform: capitalize;">{{ $campaign->type }}</td>
                            <td style="padding:.75rem;">
                                <span style="padding: 0.25rem 0.75rem; background-color: #e2e8f0; color: #4a5568; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize;">
                                    {{ $campaign->status }}
                                </span>
                            </td>
                            <td style="padding:.75rem;">
                                {{ $campaign->start_date->format('M d, Y') }} - 
                                {{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Ongoing' }}
                            </td>
                            <td style="padding:.75rem;">
                                <a href="{{ route('client.campaigns.report', $campaign->id) }}" class="btn btn-primary btn-sm">View Report</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:1rem; text-align:center; color:#718096;">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:1rem;">
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection



