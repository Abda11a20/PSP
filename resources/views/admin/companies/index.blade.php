@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Company Management</h1>
            <p style="color: #718096;">Manage all companies in the system</p>
        </div>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">+ Create Company</a>
    </div>

    {{-- Statistics Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Companies</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['total'] }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">With Plans</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['with_plans'] }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Campaigns</h3>
            <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $stats['active_campaigns'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.companies.index') }}" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="min-width: 100px;">
                <label class="form-label">Per Page</label>
                <select name="per_page" class="form-control">
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Clear</a>
            </div>
        </form>
    </div>

    {{-- Companies Table --}}
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">All Companies</h2>
        
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
                        <th style="padding: 0.75rem; text-align: left;">Name</th>
                        <th style="padding: 0.75rem; text-align: left;">Email</th>
                        <th style="padding: 0.75rem; text-align: left;">Plan</th>
                        <th style="padding: 0.75rem; text-align: left;">Role</th>
                        <th style="padding: 0.75rem; text-align: left;">Created</th>
                        <th style="padding: 0.75rem; text-align: left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 0.75rem;">#{{ $company->id }}</td>
                            <td style="padding: 0.75rem;">
                                <strong>{{ $company->name }}</strong>
                            </td>
                            <td style="padding: 0.75rem;">{{ $company->email }}</td>
                            <td style="padding: 0.75rem;">
                                @if($company->plan)
                                    <span style="padding: 0.25rem 0.75rem; background-color: #bee3f8; color: #2c5282; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                        {{ $company->plan->name }}
                                    </span>
                                @else
                                    <span style="color: #718096;">No Plan</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem;">
                                <span style="padding: 0.25rem 0.75rem; background-color: {{ $company->role === 'admin' ? '#fefcbf' : '#e2e8f0' }}; color: {{ $company->role === 'admin' ? '#744210' : '#4a5568' }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                                    {{ $company->role ?? 'client' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">{{ $company->created_at->format('M d, Y') }}</td>
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-primary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">View</a>
                                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Edit</a>
                                    <a href="{{ route('admin.companies.statistics', $company->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.875rem; padding: 0.25rem 0.75rem;">Stats</a>
                                    @if($company->id !== $currentUser->id)
                                        <form method="POST" action="{{ route('admin.companies.destroy', $company->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this company? This will delete all associated data.');">
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
                            <td colspan="7" style="padding: 2rem; text-align: center; color: #718096;">
                                No companies found. <a href="{{ route('admin.companies.create') }}" style="color: #2b6cb0;">Create your first company</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($companies->hasPages())
            <div style="margin-top: 2rem;">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


