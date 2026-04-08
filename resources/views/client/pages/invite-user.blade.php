@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #e2e8f0;">
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Invite New User</h1>
            <p style="color: #718096; font-size: 0.875rem;">Send an invitation to a new user to join your company.</p>
        </div>

        <div style="padding: 1.5rem;">
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

            <form method="POST" action="{{ route('client.users.invite.store') }}">
                @csrf

                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #2d3748;">
                        Full Name <span style="color: #e53e3e;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 0.375rem; font-size: 1rem;"
                        placeholder="Enter user's full name"
                    >
                    @error('name')
                        <p style="color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #2d3748;">
                        Email Address <span style="color: #e53e3e;">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 0.375rem; font-size: 1rem;"
                        placeholder="user@example.com"
                    >
                    @error('email')
                        <p style="color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="role" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #2d3748;">
                        Role <span style="color: #e53e3e;">*</span>
                    </label>
                    <select 
                        id="role" 
                        name="role" 
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 0.375rem; font-size: 1rem; background-color: white;"
                    >
                        <option value="">Select a role</option>
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role')
                        <p style="color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                    <p style="color: #718096; font-size: 0.875rem; margin-top: 0.5rem;">
                        <strong>User:</strong> Can view campaigns and reports<br>
                        <strong>Manager:</strong> Can create and manage campaigns<br>
                        <strong>Admin:</strong> Full access to all features
                    </p>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button 
                        type="submit" 
                        class="btn btn-primary"
                        style="flex: 1; padding: 0.75rem 1.5rem; background-color: #2b6cb0; color: white; border: none; border-radius: 0.375rem; font-weight: 600; cursor: pointer;"
                    >
                        Send Invitation
                    </button>
                    <a 
                        href="{{ route('client.users') }}" 
                        class="btn btn-secondary"
                        style="flex: 1; padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #4a5568; border: none; border-radius: 0.375rem; font-weight: 600; text-decoration: none; text-align: center; display: inline-block;"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






