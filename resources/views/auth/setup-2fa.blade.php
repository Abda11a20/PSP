@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 600px; margin: 2rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Setup Two-Factor Authentication
            </h2>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Step 1: Scan QR Code</h3>
                <p style="color: #718096; margin-bottom: 1rem;">
                    Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)
                </p>
                <div style="text-align: center; background-color: #f7fafc; padding: 2rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" style="max-width: 200px;">
                </div>
                <p style="color: #718096; font-size: 0.875rem; text-align: center;">
                    <strong>Or enter this code manually:</strong><br>
                    <code style="background-color: #e2e8f0; padding: 0.5rem 1rem; border-radius: 0.25rem; font-size: 1rem;">{{ $secret }}</code>
                </p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Step 2: Verify Setup</h3>
                <p style="color: #718096; margin-bottom: 1rem;">
                    Enter the 6-digit code from your authenticator app to verify the setup.
                </p>

                <form method="POST" action="{{ route('2fa.enable') }}">
                    @csrf

                    <div class="form-group">
                        <label for="code" class="form-label">Verification Code</label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="Enter 6-digit code"
                               maxlength="6"
                               required 
                               autofocus>
                        @error('code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                        Enable Two-Factor Authentication
                    </button>
                </form>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('client.dashboard') }}" style="color: #2b6cb0; text-decoration: none;">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>
@endsection






