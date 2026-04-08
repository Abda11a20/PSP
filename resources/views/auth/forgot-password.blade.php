@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Forgot Password
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

            <p style="color: #718096; margin-bottom: 1.5rem; text-align: center;">
                Enter your email address and choose a reset method.
            </p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Reset Method</label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="flex: 1; padding: 0.75rem; border: 2px solid #cbd5e0; border-radius: 0.375rem; cursor: pointer; text-align: center;">
                            <input type="radio" name="method" value="email" checked style="margin-right: 0.5rem;">
                            Email Link
                        </label>
                        <label style="flex: 1; padding: 0.75rem; border: 2px solid #cbd5e0; border-radius: 0.375rem; cursor: pointer; text-align: center;">
                            <input type="radio" name="method" value="otp" style="margin-right: 0.5rem;">
                            OTP Code
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Send Reset Instructions
                </button>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #2b6cb0; text-decoration: none;">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






