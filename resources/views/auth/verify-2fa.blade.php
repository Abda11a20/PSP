@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Two-Factor Authentication
            </h2>

            <p style="color: #718096; margin-bottom: 1.5rem; text-align: center;">
                Enter the 6-digit code from your authenticator app or use a recovery code.
            </p>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.store') }}">
                @csrf

                <div class="form-group">
                    <label for="code" class="form-label">Verification Code</label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           class="form-control @error('code') is-invalid @enderror"
                           placeholder="Enter 6-digit code or recovery code"
                           required 
                           autofocus
                           maxlength="10">
                    @error('code')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <small style="color: #718096; font-size: 0.875rem;">Enter the code from your authenticator app or a recovery code</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Verify
                </button>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #2b6cb0; text-decoration: none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






