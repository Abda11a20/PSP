@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 700;">Create New Company</h1>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">← Back to Companies</a>
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.companies.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Company Name <span style="color: #e53e3e;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Enter the company name
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span style="color: #e53e3e;">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    This will be used for login
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Password <span style="color: #e53e3e;">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="8">
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Minimum 8 characters
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password <span style="color: #e53e3e;">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <div class="form-group">
                <label class="form-label">Subscription Plan <span style="color: #e53e3e;">*</span></label>
                <select name="plan_id" class="form-control" required>
                    <option value="">Select a plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month ({{ $plan->employee_limit }} employees)
                        </option>
                    @endforeach
                </select>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Select the subscription plan for this company
                </small>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Create Company</button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection


