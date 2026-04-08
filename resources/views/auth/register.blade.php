@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 500px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-size: 1.875rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
                Create Your Account
            </h2>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p style="margin: 0;">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Company Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           required 
                           autofocus>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required>
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
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label for="plan_id" class="form-label">Select Plan</label>
                    <select id="plan_id" 
                            name="plan_id" 
                            class="form-control @error('plan_id') is-invalid @enderror"
                            required>
                        <option value="">Choose a plan...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month
                                ({{ $plan->employee_limit == -1 ? 'Unlimited' : $plan->employee_limit }} employees)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Create Account
                </button>

                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #2b6cb0; text-decoration: none;">
                        Already have an account? Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
