@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Reset Password
            </h2>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control"
                           value="{{ $email }}"
                           readonly
                           style="background-color: #f7fafc;">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror"
                           required 
                           autofocus>
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
                    <a href="{{ route('login') }}" style="color: #2b6cb0; text-decoration: none;">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






