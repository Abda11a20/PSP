@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Reset Password with OTP
            </h2>

            @if(session('status'))
                <div class="alert alert-success" style="background-color: #c6f6d5; color: #22543d; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.reset.otp.store') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', session('email')) }}"
                           required 
                           autofocus>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="otp" class="form-label">OTP Code</label>
                    <input type="text" 
                           id="otp" 
                           name="otp" 
                           class="form-control @error('otp') is-invalid @enderror"
                           placeholder="Enter 6-digit OTP"
                           maxlength="6"
                           required>
                    @error('otp')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <small style="color: #718096; font-size: 0.875rem;">Check your email for the OTP code</small>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           required>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control"
                           required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Reset Password
                </button>

                <div style="text-align: center;">
                    <a href="{{ route('password.request') }}" style="color: #2b6cb0; text-decoration: none;">
                        Resend OTP
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






