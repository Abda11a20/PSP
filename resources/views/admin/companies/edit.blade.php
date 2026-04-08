@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 700;">Edit Company</h1>
            <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-secondary">← Back</a>
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

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.companies.update', $company->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Company Name <span style="color: #e53e3e;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $company->name) }}" required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Enter the company name
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span style="color: #e53e3e;">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $company->email) }}" required>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    This will be used for login
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Subscription Plan <span style="color: #e53e3e;">*</span></label>
                <select name="plan_id" class="form-control" required>
                    <option value="">Select a plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id', $company->plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month ({{ $plan->employee_limit }} employees)
                        </option>
                    @endforeach
                </select>
                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                    Select the subscription plan for this company
                </small>
            </div>

            <div style="background-color: #f7fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">Company Information</h4>
                <p style="color: #718096; font-size: 0.875rem; margin: 0;">
                    Company ID: <strong>#{{ $company->id }}</strong> | 
                    Created: <strong>{{ $company->created_at->format('M d, Y') }}</strong>
                </p>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Update Company</button>
                <a href="{{ route('admin.companies.show', $company->id) }}" class="btn btn-secondary">Cancel</a>
                @if($company->id !== $currentUser->id)
                    <form method="POST" action="{{ route('admin.companies.destroy', $company->id) }}" style="display: inline; margin-left: auto;" onsubmit="return confirm('Are you sure you want to delete this company? This action cannot be undone and will delete all associated data.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Company</button>
                    </form>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection


