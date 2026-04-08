@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem;">🔔 Notifications</h1>
                <p style="color: #718096;">View all your notifications and alerts</p>
            </div>
            @if(count($notifications) > 0)
                <form method="POST" action="{{ route('client.notifications.mark-all-read') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">Mark All as Read</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(count($notifications) === 0)
            <div style="text-align: center; padding: 4rem 2rem; color: #718096;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔔</div>
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: #2d3748;">No Notifications</h3>
                <p style="font-size: 0.875rem; margin: 0;">You're all caught up! New notifications will appear here.</p>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($notifications as $notification)
                    <a href="{{ $notification['link'] }}" class="card" style="padding: 1rem; text-decoration: none; color: inherit; display: block; transition: all 0.2s; border-left: 3px solid {{ $notification['type'] === 'interaction' ? '#e53e3e' : '#2b6cb0' }};">
                        <div style="display: flex; gap: 1rem; align-items: start;">
                            <div style="font-size: 2rem; flex-shrink: 0;">{{ $notification['icon'] }}</div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1rem; font-weight: 600; color: #2d3748; margin: 0;">{{ $notification['title'] }}</h3>
                                    <span style="font-size: 0.75rem; color: #a0aec0; white-space: nowrap; margin-left: 1rem;">{{ $notification['time']->diffForHumans() }}</span>
                                </div>
                                <p style="font-size: 0.875rem; color: #718096; margin: 0 0 0.5rem 0;">{{ $notification['message'] }}</p>
                                <div style="font-size: 0.75rem; color: #a0aec0;">{{ $notification['time']->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    .card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
</style>
@endsection



