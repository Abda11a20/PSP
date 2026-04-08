@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h1 style="font-size: 1.5rem; font-weight: 700;">Manage Users</h1>
            <a href="{{ route('client.users.invite') }}" class="btn btn-primary">Invite User</a>
        </div>

        @if(session('success'))
            <div style="background-color: #c6f6d5; color: #22543d; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #9ae6b4;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: #fed7d7; color: #c53030; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #fc8181;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div style="background-color: #feebc8; color: #c05621; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem; border: 1px solid #f6ad55;">
                {{ session('warning') }}
            </div>
        @endif

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:2px solid #e2e8f0;">
                        <th style="padding:.75rem;text-align:left;">Name</th>
                        <th style="padding:.75rem;text-align:left;">Email</th>
                        <th style="padding:.75rem;text-align:left;">Role</th>
                        <th style="padding:.75rem;text-align:left;">Joined</th>
                        <th style="padding:.75rem;text-align:left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:.75rem;">{{ $user->name }}</td>
                            <td style="padding:.75rem;">{{ $user->email }}</td>
                            <td style="padding:.75rem; text-transform: capitalize;">{{ $user->role }}</td>
                            <td style="padding:.75rem;">{{ $user->created_at->format('M d, Y') }}</td>
                            <td style="padding:.75rem;">
                                <a href="{{ route('client.users.edit', $user->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('client.users.delete', $user->id) }}" method="POST" style="display: inline-block; margin-left: 0.5rem;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this user? This action cannot be undone.');">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:1rem; text-align:center; color:#718096;">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:1rem;">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection



