@extends('layouts.app')

@section('content')
<div class="container">
    <div style="max-width: 800px; margin: 2rem auto;">
        <div class="card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Contact Support</h1>
                <p style="color: #718096; font-size: 1.125rem;">
                    We're here to help! Send us a message and we'll get back to you as soon as possible.
                </p>
            </div>

            @if(session('success'))
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info" style="background-color: #bee3f8; color: #2c5282; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; border: 1px solid #90cdf4;">
                    <strong>ℹ️ Information:</strong> {{ session('info') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact-support.submit') }}">
                @csrf

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div class="form-group">
                        <label for="name" class="form-label">Your Name <span style="color: #e53e3e;">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $company ? $company->name : '') }}"
                               required 
                               autofocus>
                        @error('name')
                            <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span style="color: #e53e3e;">*</span></label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $company ? $company->email : '') }}"
                               required>
                        @error('email')
                            <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="subject" class="form-label">Subject <span style="color: #e53e3e;">*</span></label>
                    <input type="text" 
                           id="subject" 
                           name="subject" 
                           class="form-control @error('subject') is-invalid @enderror"
                           value="{{ old('subject') }}"
                           placeholder="Brief description of your issue"
                           required>
                    @error('subject')
                        <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="priority" class="form-label">Priority</label>
                    <select id="priority" 
                            name="priority" 
                            class="form-control @error('priority') is-invalid @enderror">
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium - General inquiry</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - Non-urgent</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High - Important</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent - Critical issue</option>
                    </select>
                    @error('priority')
                        <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Select the priority level for your request
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="message" class="form-label">Message <span style="color: #e53e3e;">*</span></label>
                    <textarea id="message" 
                              name="message" 
                              class="form-control @error('message') is-invalid @enderror"
                              rows="8"
                              placeholder="Please provide as much detail as possible about your issue or question..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="text-danger" style="margin-top: 0.25rem; font-size: 0.875rem;">{{ $message }}</div>
                    @enderror
                    <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Maximum 5000 characters
                    </small>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 200px;">
                        Send Message
                    </button>
                    <a href="{{ Auth::guard('company')->check() ? route('client.dashboard') : route('home') }}" 
                       class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: #2d3748;">Other Ways to Reach Us</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">📧 Email</h4>
                        <p style="color: #718096; margin: 0;">
                            <a href="mailto:support@phishingsim.com" style="color: #2b6cb0; text-decoration: none;">
                                support@phishingsim.com
                            </a>
                        </p>
                    </div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">📞 Phone</h4>
                        <p style="color: #718096; margin: 0;">
                            <a href="tel:+1-800-555-0123" style="color: #2b6cb0; text-decoration: none;">
                                +1-800-555-0123
                            </a>
                        </p>
                        <small style="color: #a0aec0; font-size: 0.75rem;">Mon-Fri, 9AM-5PM EST</small>
                    </div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">⏰ Response Time</h4>
                        <p style="color: #718096; margin: 0;">
                            We typically respond within 24 hours
                        </p>
                    </div>
                </div>
            </div>

            @if(Auth::guard('company')->check())
                <div style="margin-top: 2rem; padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem; border-left: 4px solid #667eea;">
                    <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: #4a5568;">💡 Quick Tips</h4>
                    <ul style="margin: 0; padding-left: 1.25rem; color: #718096; font-size: 0.875rem;">
                        <li>Check our <a href="{{ route('client.upgrade-plan') }}" style="color: #2b6cb0;">Upgrade Plan</a> page for subscription questions</li>
                        <li>Review your <a href="{{ route('client.reports') }}" style="color: #2b6cb0;">Reports</a> for campaign analytics</li>
                        <li>Visit <a href="{{ route('client.dashboard') }}" style="color: #2b6cb0;">Dashboard</a> for quick access to all features</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    textarea.form-control {
        resize: vertical;
        min-height: 150px;
    }
</style>
@endsection

