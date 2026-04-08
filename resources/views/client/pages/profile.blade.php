@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">👤 Profile</h1>
        <p style="color: #718096; margin-bottom: 1.5rem;">Manage your account information and settings.</p>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin-bottom: 2rem;">
            {{-- Profile Information Card --}}
            <div>
                <div class="card" style="padding: 1.5rem; text-align: center;">
                    <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">
                        {{ strtoupper(substr($company->name, 0, 1)) }}
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $company->name }}</h2>
                    <p style="color: #718096; margin-bottom: 1rem;">{{ $company->email }}</p>
                    @if($company->plan)
                        <div style="padding: 0.5rem 1rem; background-color: #c6f6d5; color: #22543d; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                            {{ $company->plan->name }} Plan
                        </div>
                    @endif
                    @if($company->role)
                        <div style="margin-top: 1rem; padding: 0.5rem 1rem; background-color: #bee3f8; color: #2c5282; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block; text-transform: capitalize;">
                            {{ $company->role }}
                        </div>
                    @endif
                </div>

                {{-- Account Statistics --}}
                <div class="card" style="padding: 1.5rem; margin-top: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem;">Account Statistics</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #718096;">Campaigns</span>
                            <span style="font-weight: 600; color: #2d3748;">{{ $company->campaigns()->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #718096;">Users</span>
                            <span style="font-weight: 600; color: #2d3748;">{{ $company->users()->count() }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #718096;">Member Since</span>
                            <span style="font-weight: 600; color: #2d3748;">{{ $company->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Profile Form --}}
            <div>
                <div class="card" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Edit Profile Information</h3>
                    
                    <form method="POST" action="{{ route('client.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Company Name <span style="color: red;">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}" required>
                            <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                Your email address is used for login and notifications.
                            </small>
                        </div>

                        <div style="background-color: #f7fafc; padding: 1rem; border-radius: 0.5rem; margin: 1.5rem 0;">
                            <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">Current Plan</h4>
                            @if($company->plan)
                                <p style="color: #2d3748; font-weight: 600; margin-bottom: 0.5rem;">{{ $company->plan->name }}</p>
                                <p style="color: #718096; font-size: 0.875rem; margin: 0;">
                                    @if($company->plan->price > 0)
                                        ${{ number_format($company->plan->price, 2) }}/month
                                    @else
                                        Free Plan
                                    @endif
                                </p>
                                <a href="{{ route('client.upgrade-plan') }}" class="btn btn-secondary" style="margin-top: 1rem; display: inline-block;">
                                    Upgrade Plan
                                </a>
                            @else
                                <p style="color: #718096; font-size: 0.875rem; margin: 0;">No plan assigned</p>
                                <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary" style="margin-top: 1rem; display: inline-block;">
                                    Choose a Plan
                                </a>
                            @endif
                        </div>

                        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>

                {{-- Change Password Form --}}
                <div class="card" style="padding: 1.5rem; margin-top: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Change Password</h3>
                    
                    <form method="POST" action="{{ route('client.profile.change-password') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Current Password <span style="color: red;">*</span></label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">New Password <span style="color: red;">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="8">
                            <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                Password must be at least 8 characters long.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm New Password <span style="color: red;">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required minlength="8">
                        </div>

                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



