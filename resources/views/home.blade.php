@extends('layouts.app')

@section('content')
<div class="container">
    <div style="text-align: center; padding: 4rem 0;">
        <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1.5rem;">
            Welcome to Phishing Simulation Platform
        </h1>
        <p style="font-size: 1.25rem; color: #718096; margin-bottom: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Strengthen your organization's security with realistic phishing simulations and comprehensive security awareness training.
        </p>
        
        <div style="display: flex; gap: 1rem; justify-content: center;">
            @guest('company')
                <a href="{{ route('register') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">
                    Start Free Trial
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">
                    Login
                </a>
            @else
                @php
                    $user = Auth::guard('company')->user();
                    $role = $user->role ?? 'client';
                @endphp
                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">
                        Go to Admin Dashboard
                    </a>
                @elseif($role === 'developer')
                    <a href="{{ route('api.documentation') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">
                        Go to API Documentation
                    </a>
                @else
                    <a href="{{ route('client.dashboard') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 0.75rem 2rem;">
                        Go to Dashboard
                    </a>
                @endif
            @endguest
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 4rem;">
        <div class="card">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: #2b6cb0;">🎯 Phishing Simulations</h3>
            <p style="color: #718096;">
                Run realistic phishing campaigns to test your employees' security awareness and identify vulnerabilities.
            </p>
        </div>

        <div class="card">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: #2b6cb0;">📊 Detailed Analytics</h3>
            <p style="color: #718096;">
                Get comprehensive reports on campaign performance, employee engagement, and security improvements over time.
            </p>
        </div>

        <div class="card">
            <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: #2b6cb0;">🤖 AI-Powered Insights</h3>
            <p style="color: #718096;">
                Leverage artificial intelligence to analyze results and receive personalized training recommendations.
            </p>
        </div>
    </div>

    <div class="card" style="margin-top: 4rem; text-align: center;">
        <h2 style="font-size: 2rem; margin-bottom: 2rem;">Choose Your Plan</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div style="padding: 2rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Free</h3>
                <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin-bottom: 1rem;">$0<span style="font-size: 1rem; font-weight: 400;">/month</span></p>
                <ul style="list-style: none; text-align: left; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ Up to 10 employees</li>
                    <li style="padding: 0.5rem 0;">✓ Basic phishing simulations</li>
                    <li style="padding: 0.5rem 0;">✓ Email templates</li>
                    <li style="padding: 0.5rem 0;">✓ Basic reporting</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">Get Started</a>
            </div>

            <div style="padding: 2rem; border: 2px solid #2b6cb0; border-radius: 0.5rem; position: relative;">
                <span style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: white; padding: 0 0.5rem; color: #2b6cb0; font-weight: 600;">Popular</span>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Standard</h3>
                <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin-bottom: 1rem;">$79.99<span style="font-size: 1rem; font-weight: 400;">/month</span></p>
                <ul style="list-style: none; text-align: left; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ Up to 200 employees</li>
                    <li style="padding: 0.5rem 0;">✓ Advanced simulations</li>
                    <li style="padding: 0.5rem 0;">✓ AI-powered analysis</li>
                    <li style="padding: 0.5rem 0;">✓ Priority support</li>
                </ul>
                <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">Get Started</a>
            </div>

            <div style="padding: 2rem; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Enterprise</h3>
                <p style="font-size: 2rem; font-weight: 700; color: #2b6cb0; margin-bottom: 1rem;">Custom</p>
                <ul style="list-style: none; text-align: left; margin-bottom: 1.5rem;">
                    <li style="padding: 0.5rem 0;">✓ Unlimited employees</li>
                    <li style="padding: 0.5rem 0;">✓ Custom integrations</li>
                    <li style="padding: 0.5rem 0;">✓ Dedicated support</li>
                    <li style="padding: 0.5rem 0;">✓ On-premise option</li>
                </ul>
                <a href="#" class="btn btn-secondary" style="width: 100%;">Contact Sales</a>
            </div>
        </div>
    </div>
</div>
@endsection
