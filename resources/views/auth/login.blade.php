@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Welcome Back
            </h2>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
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
                    <label for="password" class="form-label">Password</label>
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
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" 
                               name="remember" 
                               id="remember" 
                               style="margin-right: 0.5rem;">
                        <span style="color: #4a5568;">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Login
                </button>

                <div style="text-align: center; margin-bottom: 1rem;">
                    <a href="{{ route('password.request') }}" style="color: #2b6cb0; text-decoration: none;">
                        Forgot your password?
                    </a>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('register') }}" style="color: #2b6cb0; text-decoration: none;">
                        Don't have an account? Register
                    </a>
                </div>
            </form>
        </div>

        <div style="margin-top: 2rem; text-align: center;">
            <p style="color: #718096; margin-bottom: 1rem;">Demo Credentials:</p>
            <div style="background-color: #f7fafc; padding: 1rem; border-radius: 0.375rem;">
                <p style="margin: 0.25rem 0;"><strong>Admin:</strong> admin@acme.com / password123</p>
                <p style="margin: 0.25rem 0;"><strong>Client:</strong> admin@techstart.com / password123</p>
            </div>
        </div>
    </div>
</div>
@endsection
