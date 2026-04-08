@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700;">Add Targets to Campaign</h1>
                <p style="color: #718096;">Campaign #{{ $campaign->id }} - {{ ucfirst($campaign->type) }}</p>
            </div>
            <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">← Back to Campaign</a>
        </div>

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <strong>Error:</strong> {{ session('error') }}
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

        @php
            // Calculate current total targets across all campaigns
            $currentTotalTargets = \App\Models\CampaignTarget::whereHas('campaign', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count();
            
            $plan = $company->plan;
            $employeeLimit = $plan->employee_limit;
            $remainingSlots = $employeeLimit == -1 ? 'Unlimited' : max(0, $employeeLimit - $currentTotalTargets);
        @endphp

        <div class="card" style="background-color: #e6f3ff; border-left: 4px solid #2b6cb0; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; color: #2d3748;">Plan Limit Information</h3>
                    <p style="color: #4a5568; font-size: 0.875rem; margin: 0;" id="limit-info">
                        <strong>Current Plan:</strong> {{ $plan->name }} | 
                        <strong>Current Targets:</strong> {{ $currentTotalTargets }} / 
                        {{ $employeeLimit == -1 ? 'Unlimited' : $employeeLimit }}
                        @if($employeeLimit != -1)
                            | <strong>Remaining:</strong> {{ $remainingSlots }}
                        @endif
                    </p>
                </div>
                @if($employeeLimit != -1 && $currentTotalTargets >= $employeeLimit)
                    <a href="{{ route('client.upgrade-plan') }}" class="btn btn-primary btn-sm">
                        Upgrade Plan
                    </a>
                @endif
            </div>
        </div>

        @if($campaign->targets->count() > 0)
            <div class="card" style="background-color: #f7fafc; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; margin-bottom: 1rem;">Existing Targets ({{ $campaign->targets->count() }})</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 0.5rem; text-align: left;">Name</th>
                                <th style="padding: 0.5rem; text-align: left;">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($campaign->targets as $target)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 0.5rem;">{{ $target->name }}</td>
                                    <td style="padding: 0.5rem;">{{ $target->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('client.campaigns.store-targets', $campaign->id) }}" id="targetsForm">
            @csrf

            <div id="targetsContainer">
                <div class="target-row" style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;">
                    <div class="form-group">
                        <label class="form-label">Name <span style="color: #e53e3e;">*</span></label>
                        <input type="text" name="targets[0][name]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span style="color: #e53e3e;">*</span></label>
                        <input type="email" name="targets[0][email]" class="form-control" required>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeTarget(this)" style="display: none;">Remove</button>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="addTarget()">+ Add Another Target</button>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Add Targets</button>
                <a href="{{ route('client.campaigns.show', $campaign->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    let targetCount = 1;
    const employeeLimit = {{ $employeeLimit == -1 ? 'null' : $employeeLimit }};
    const currentTotalTargets = {{ $currentTotalTargets }};
    const remainingSlots = {{ $employeeLimit == -1 ? 'null' : $remainingSlots }};

    function addTarget() {
        // Check if limit is reached
        if (employeeLimit !== null) {
            const currentRows = document.querySelectorAll('.target-row').length;
            const newTotal = currentTotalTargets + currentRows;
            
            if (newTotal >= employeeLimit) {
                alert('You have reached the limit for your plan. You cannot add more targets. Please upgrade your plan to add more targets.');
                return;
            }
        }
        
        const container = document.getElementById('targetsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'target-row';
        newRow.style.cssText = 'display: grid; grid-template-columns: 1fr 2fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;';
        
        newRow.innerHTML = `
            <div class="form-group">
                <label class="form-label">Name <span style="color: #e53e3e;">*</span></label>
                <input type="text" name="targets[${targetCount}][name]" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email <span style="color: #e53e3e;">*</span></label>
                <input type="email" name="targets[${targetCount}][email]" class="form-control" required>
            </div>
            <button type="button" class="btn btn-danger" onclick="removeTarget(this)">Remove</button>
        `;
        
        container.appendChild(newRow);
        targetCount++;
        
        // Show remove buttons on all rows if more than one
        updateRemoveButtons();
        updateLimitInfo();
    }

    function removeTarget(button) {
        button.closest('.target-row').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.target-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('button');
            if (rows.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
        updateLimitInfo();
    }

    function updateLimitInfo() {
        if (employeeLimit === null) return; // Unlimited
        
        const currentRows = document.querySelectorAll('.target-row').length;
        const newTotal = currentTotalTargets + currentRows;
        const newRemaining = Math.max(0, employeeLimit - newTotal);
        
        // Update the limit info display if element exists
        const limitInfo = document.getElementById('limit-info');
        if (limitInfo) {
            limitInfo.textContent = `Current Targets: ${currentTotalTargets} / ${employeeLimit} | Remaining: ${newRemaining}`;
            
            if (newRemaining === 0) {
                limitInfo.style.color = '#e53e3e';
                limitInfo.style.fontWeight = '600';
            } else {
                limitInfo.style.color = '#4a5568';
                limitInfo.style.fontWeight = 'normal';
            }
        }
    }

    // Validate before form submission
    document.getElementById('targetsForm').addEventListener('submit', function(e) {
        if (employeeLimit !== null) {
            const currentRows = document.querySelectorAll('.target-row').length;
            const newTotal = currentTotalTargets + currentRows;
            
            if (newTotal > employeeLimit) {
                e.preventDefault();
                alert('You cannot add more targets than your plan allows. Your plan limit is ' + employeeLimit + ' targets. Please remove some targets or upgrade your plan.');
                return false;
            }
        }
    });

    // Initialize limit info on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLimitInfo();
    });
</script>
@endsection


