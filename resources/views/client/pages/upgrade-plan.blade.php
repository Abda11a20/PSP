@extends('layouts.app')

@section('content')
<div class="container">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Upgrade Your Plan</h1>
        <p style="color: #718096;">Choose a plan that best fits your needs</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Current Plan Card -->
    <div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Your Current Plan</h2>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <p style="font-size: 2rem; font-weight: 700; margin: 0;">{{ $company->plan->name }}</p>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">
                    ${{ number_format($company->plan->price, 2) }}/month
                </p>
                <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">
                    {{ $company->plan->employee_limit == -1 ? 'Unlimited' : $company->plan->employee_limit }} employees
                </p>
            </div>
            <div style="padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.2); border-radius: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.3);">
                <span style="font-weight: 600;">Active</span>
            </div>
        </div>
    </div>

    <!-- Available Plans -->
    <div class="card">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Available Plans</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            @foreach($plans as $plan)
                @php
                    $isCurrentPlan = $plan->id === $company->plan_id;
                    $isUpgrade = $plan->price > $company->plan->price;
                    $isDowngrade = $plan->price < $company->plan->price && $plan->price > 0;
                @endphp
                
                <div class="card" style="padding: 1.5rem; position: relative; {{ $isCurrentPlan ? 'border: 2px solid #667eea; background: #f7fafc;' : '' }}">
                    @if($isCurrentPlan)
                        <div style="position: absolute; top: -0.75rem; right: 1rem; background: #667eea; color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                            Current Plan
                        </div>
                    @endif
                    
                    <div style="margin-bottom: 1rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: #2d3748;">
                            {{ $plan->name }}
                        </h3>
                        <div style="display: flex; align-items: baseline; margin-bottom: 0.5rem;">
                            <span style="font-size: 2rem; font-weight: 700; color: #2b6cb0;">
                                ${{ number_format($plan->price, 2) }}
                            </span>
                            <span style="color: #718096; margin-left: 0.25rem;">/month</span>
                        </div>
                        <p style="color: #718096; font-size: 0.875rem; margin: 0;">
                            {{ $plan->employee_limit == -1 ? 'Unlimited' : number_format($plan->employee_limit) }} employees
                        </p>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        @if($isCurrentPlan)
                            <button class="btn btn-secondary" disabled style="width: 100%;">
                                Current Plan
                            </button>
                        @elseif($isUpgrade)
                            <button 
                                class="btn btn-primary upgrade-plan-btn" 
                                data-plan-id="{{ $plan->id }}"
                                data-plan-name="{{ $plan->name }}"
                                data-plan-price="{{ $plan->price }}"
                                style="width: 100%;">
                                Upgrade to {{ $plan->name }}
                            </button>
                        @elseif($isDowngrade)
                            <button class="btn btn-secondary" disabled style="width: 100%;">
                                Downgrade (Contact Support)
                            </button>
                        @else
                            <button class="btn btn-secondary" disabled style="width: 100%;">
                                Same Price Plan
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="paymentModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="card" style="max-width: 400px; margin: 2rem; position: relative;">
            <div id="paymentModalContent">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Processing Payment...</h3>
                <p style="color: #718096; margin-bottom: 1rem;">Please wait while we initialize your payment.</p>
                <div style="text-align: center; padding: 1rem;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const upgradeButtons = document.querySelectorAll('.upgrade-plan-btn');
    const paymentModal = document.getElementById('paymentModal');
    const paymentModalContent = document.getElementById('paymentModalContent');

    upgradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const planId = this.getAttribute('data-plan-id');
            const planName = this.getAttribute('data-plan-name');
            const planPrice = this.getAttribute('data-plan-price');

            // Show modal
            paymentModal.style.display = 'flex';
            paymentModalContent.innerHTML = `
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Processing Payment...</h3>
                <p style="color: #718096; margin-bottom: 1rem;">Initializing checkout for ${planName} plan.</p>
                <div style="text-align: center; padding: 1rem;">
                    <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                </div>
            `;

            // Disable button
            this.disabled = true;
            this.textContent = 'Processing...';

            // Submit form to web route
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("client.upgrade-plan.checkout") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            const planIdInput = document.createElement('input');
            planIdInput.type = 'hidden';
            planIdInput.name = 'plan_id';
            planIdInput.value = planId;
            form.appendChild(planIdInput);
            
            document.body.appendChild(form);
            form.submit();
        });
    });

});
</script>
@endsection

