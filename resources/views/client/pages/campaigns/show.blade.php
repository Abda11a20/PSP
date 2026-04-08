@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;">Campaign Details</h1>
            <p style="color: #718096;">Campaign #{{ $campaign->id }} - {{ ucfirst($campaign->type) }}</p>
        </div>
        <div>
            <a href="{{ route('client.campaigns.index') }}" class="btn btn-secondary">← Back to Campaigns</a>
            @if($campaign->status === 'draft')
                <a href="{{ route('client.campaigns.edit', $campaign->id) }}" class="btn btn-primary" style="margin-left: 0.5rem;">Edit</a>
            @endif
        </div>
    </div>

    {{-- Flash Messages - Fixed at top --}}
    <div style="position: sticky; top: 80px; z-index: 100; margin-bottom: 1rem;">
        @if(session('success'))
            <div id="flash-message" class="alert alert-success" style="position: relative; padding-right: 2.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; opacity: 0.7; padding: 0; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div id="flash-message" class="alert alert-error" style="position: relative; padding-right: 2.5rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; font-size: 1.25rem; cursor: pointer; color: inherit; opacity: 0.7; padding: 0; width: 1.5rem; height: 1.5rem; display: flex; align-items: center; justify-content: center; line-height: 1;">&times;</button>
            </div>
        @endif
    </div>

    {{-- Campaign Info --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Information</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Campaign Type</h4>
                <p style="font-size: 1.25rem; font-weight: 600; text-transform: capitalize;">{{ $campaign->type }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Status</h4>
                @php
                    $statusColors = [
                        'active' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                        'running' => ['bg' => '#c6f6d5', 'text' => '#22543d'],
                        'draft' => ['bg' => '#e2e8f0', 'text' => '#4a5568'],
                        'completed' => ['bg' => '#bee3f8', 'text' => '#2c5282'],
                        'paused' => ['bg' => '#fefcbf', 'text' => '#744210'],
                    ];
                    $color = $statusColors[$campaign->status] ?? ['bg' => '#e2e8f0', 'text' => '#4a5568'];
                @endphp
                <span style="padding: 0.5rem 1rem; background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize; font-weight: 600;">
                    {{ $campaign->status }}
                </span>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Start Date</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'Not set' }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">End Date</h4>
                <p style="font-size: 1.25rem; font-weight: 600;">{{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Not set' }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Created</h4>
                <p style="font-size: 1rem; color: #718096;">{{ $campaign->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <h4 style="color: #718096; margin-bottom: 0.5rem; font-size: 0.875rem;">Last Updated</h4>
                <p style="font-size: 1rem; color: #718096;">{{ $campaign->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Total Targets</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalTargets }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Emails Sent</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalSent }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Opened</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalOpened }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Clicked</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalClicked }}</p>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
            <h3 style="font-size: 0.875rem; margin-bottom: 0.5rem; opacity: 0.9;">Submitted</h3>
            <p style="font-size: 2.5rem; font-weight: 700; margin: 0;">{{ $totalSubmitted }}</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="card" style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Actions</h2>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            {{-- Status Control Actions --}}
            @if($campaign->status === 'draft')
                @if($totalTargets > 0)
                    <form method="POST" action="{{ route('client.campaigns.launch', $campaign->id) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to launch this campaign? This will activate it and allow sending emails.');">
                            🚀 Launch Campaign
                        </button>
                    </form>
                @else
                    <div style="padding: 0.75rem; background-color: #fefcbf; color: #744210; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                        ⚠️ Add targets before launching the campaign
                    </div>
                @endif
            @endif
            
            @if(in_array($campaign->status, ['active', 'running']))
                <form method="POST" action="{{ route('client.campaigns.pause', $campaign->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to pause this campaign?');">
                        ⏸️ Pause Campaign
                    </button>
                </form>
                <form method="POST" action="{{ route('client.campaigns.stop', $campaign->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to stop this campaign? This will mark it as completed.');">
                        ⏹️ Stop Campaign
                    </button>
                </form>
            @endif

            @if($campaign->status === 'paused')
                <form method="POST" action="{{ route('client.campaigns.launch', $campaign->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        ▶️ Resume Campaign
                    </button>
                </form>
            @endif

            <div style="width: 100%; border-top: 1px solid #e2e8f0; margin: 1rem 0;"></div>

            {{-- Management Actions --}}
            @if($campaign->status === 'draft')
                <a href="{{ route('client.campaigns.add-targets', $campaign->id) }}" class="btn btn-secondary">
                    👥 Add Targets
                </a>
            @endif

            @if($totalTargets > 0)
                <a href="{{ route('client.campaigns.stats', $campaign->id) }}" class="btn btn-secondary">
                    📊 Statistics
                </a>
                <a href="{{ route('client.campaigns.ai-analysis', $campaign->id) }}" class="btn btn-secondary">
                    🤖 AI Analysis
                </a>
                <a href="{{ route('client.campaigns.report', $campaign->id) }}" class="btn btn-secondary">
                    📈 View Report
                </a>
            @endif
            
            {{-- Email Actions --}}
            @if(in_array($campaign->status, ['active', 'running']) && $totalTargets > 0)
                <form method="POST" action="{{ route('client.campaigns.send-emails', $campaign->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to send emails to all {{ $totalTargets }} target(s)?');">
                        📧 Send Emails to All Targets
                    </button>
                </form>
            @elseif($campaign->status === 'draft' && $totalTargets > 0)
                <div style="padding: 0.75rem; background-color: #bee3f8; color: #2c5282; border-radius: 0.375rem; font-size: 0.875rem;">
                    💡 Launch the campaign first to send emails
                </div>
            @endif
        </div>
    </div>

    {{-- Targets List --}}
    @if($campaign->targets->count() > 0)
        <div class="card" style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Campaign Targets ({{ $totalTargets }})</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Name</th>
                            <th style="padding: 0.75rem; text-align: left;">Email</th>
                            <th style="padding: 0.75rem; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->targets as $target)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">{{ $target->name }}</td>
                                <td style="padding: 0.75rem;">{{ $target->email }}</td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <form method="POST" action="{{ route('client.campaigns.resend-email', [$campaign->id, $target->id]) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to resend the email to {{ $target->email }}?');">
                                            Resend Email
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="margin-bottom: 2rem;">
            <div style="text-align: center; padding: 2rem; color: #718096;">
                <p style="font-size: 1.125rem; margin-bottom: 1rem;">No targets added to this campaign yet.</p>
                <a href="/api/campaign/{{ $campaign->id }}/details" class="btn btn-primary" target="_blank">Add Targets</a>
            </div>
        </div>
    @endif

    {{-- Submitted Credentials Section --}}
    @if(isset($submittedCredentials) && count($submittedCredentials) > 0)
        <div class="card" style="margin-bottom: 2rem; border-left: 4px solid #e53e3e;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #e53e3e;">⚠️ Submitted Credentials</h2>
                    <p style="color: #718096; font-size: 0.875rem; margin: 0;">
                        {{ count($submittedCredentials) }} {{ count($submittedCredentials) === 1 ? 'target has' : 'targets have' }} submitted credentials in this campaign
                    </p>
                </div>
                <div style="padding: 0.75rem 1.5rem; background-color: #fed7d7; color: #c53030; border-radius: 9999px; font-weight: 600; font-size: 1.25rem;">
                    {{ count($submittedCredentials) }}
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; background-color: #f7fafc;">
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600; width: 30px;"></th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Target Name</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Email</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Submitted At</th>
                            <th style="padding: 0.75rem; text-align: left; font-weight: 600;">Time Ago</th>
                            <th style="padding: 0.75rem; text-align: center; font-weight: 600;">Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submittedCredentials as $index => $submission)
                            <tr class="submission-row" style="border-bottom: 1px solid #e2e8f0; cursor: pointer;" onclick="toggleDetails({{ $index }})">
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span class="expand-icon" id="icon-{{ $index }}" style="font-size: 0.875rem; color: #718096;">▶</span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                            {{ strtoupper(substr($submission['target_name'], 0, 1)) }}
                                        </span>
                                        <span style="font-weight: 600;">{{ $submission['target_name'] }}</span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <a href="mailto:{{ $submission['email'] }}" style="color: #2b6cb0; text-decoration: none;" onclick="event.stopPropagation();">{{ $submission['email'] }}</a>
                                </td>
                                <td style="padding: 0.75rem; color: #718096;">
                                    {{ $submission['submitted_at'] }}
                                </td>
                                <td style="padding: 0.75rem; color: #a0aec0; font-size: 0.875rem;">
                                    {{ $submission['time_ago'] }}
                                </td>
                                <td style="padding: 0.75rem; text-align: center;">
                                    <span style="padding: 0.375rem 0.75rem; background-color: #fed7d7; color: #c53030; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                                        🔴 High Risk
                                    </span>
                                </td>
                            </tr>
                            <tr class="submission-details" id="details-{{ $index }}" style="display: none; background-color: #f7fafc;">
                                <td colspan="6" style="padding: 1.5rem;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                                        <div>
                                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #4a5568; margin-bottom: 0.75rem; text-transform: uppercase;">Submission Details</h4>
                                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Target Name</span>
                                                    <span style="font-weight: 600; color: #2d3748;">{{ $submission['target_name'] }}</span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Email Address</span>
                                                    <span style="color: #2d3748;">{{ $submission['email'] }}</span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Department</span>
                                                    <span style="color: #2d3748;">
                                                        @if($submission['department'] !== 'N/A')
                                                            {{ $submission['department'] }}
                                                        @else
                                                            <span style="color: #a0aec0; font-style: italic;">Not provided</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Password Provided</span>
                                                    <span style="color: {{ $submission['password_provided'] ? '#c53030' : '#22543d' }}; font-weight: 600;">
                                                        {{ $submission['password_provided'] ? 'Yes ⚠️' : 'No ✅' }}
                                                    </span>
                                                </div>
                                                @if($submission['password_provided'] && !empty($submission['password']))
                                                    <div>
                                                        <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Password Entered</span>
                                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                            <span style="color: #c53030; font-family: monospace; font-size: 0.875rem; font-weight: 600; background-color: #fed7d7; padding: 0.375rem 0.75rem; border-radius: 0.25rem; letter-spacing: 0.05em; min-width: 120px;" id="password-{{ $submission['interaction_id'] }}">
                                                                {{ str_repeat('•', strlen($submission['password'])) }}
                                                            </span>
                                                            <button onclick="togglePassword({{ $submission['interaction_id'] }})" data-password="{{ htmlspecialchars($submission['password'], ENT_QUOTES, 'UTF-8') }}" style="padding: 0.25rem 0.5rem; background-color: #2b6cb0; color: white; border: none; border-radius: 0.25rem; font-size: 0.75rem; cursor: pointer;" id="toggle-btn-{{ $submission['interaction_id'] }}">
                                                                👁️ Show
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #4a5568; margin-bottom: 0.75rem; text-transform: uppercase;">Technical Information</h4>
                                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">IP Address</span>
                                                    <span style="color: #2d3748; font-family: monospace; font-size: 0.875rem;">
                                                        @if($submission['ip_address'] !== 'N/A')
                                                            {{ $submission['ip_address'] }}
                                                        @else
                                                            <span style="color: #a0aec0; font-style: italic;">Not available</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">User Agent</span>
                                                    <span style="color: #2d3748; font-size: 0.875rem; word-break: break-all;">
                                                        @if($submission['user_agent'] !== 'N/A')
                                                            {{ Str::limit($submission['user_agent'], 80) }}
                                                        @else
                                                            <span style="color: #a0aec0; font-style: italic;">Not available</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Campaign Type</span>
                                                    <span style="color: #2d3748; text-transform: capitalize;">{{ $submission['campaign_type'] }}</span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Template Used</span>
                                                    <span style="color: #2d3748;">
                                                        @if($submission['template_name'] !== 'N/A')
                                                            {{ $submission['template_name'] }}
                                                        @else
                                                            <span style="color: #a0aec0; font-style: italic;">Not available</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <h4 style="font-size: 0.875rem; font-weight: 600; color: #4a5568; margin-bottom: 0.75rem; text-transform: uppercase;">Timeline</h4>
                                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Submitted At</span>
                                                    <span style="color: #2d3748; font-weight: 600;">{{ $submission['submitted_at'] }}</span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.25rem;">Time Ago</span>
                                                    <span style="color: #2d3748;">{{ $submission['time_ago'] }}</span>
                                                </div>
                                                <div>
                                                    <span style="font-size: 0.75rem; color: #718096; display: block; margin-bottom: 0.5rem;">Interaction ID</span>
                                                    <span style="color: #2d3748; font-family: monospace; font-size: 0.875rem;">#{{ $submission['interaction_id'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 1.5rem; padding: 1rem; background-color: #fff5f5; border-radius: 0.5rem; border-left: 3px solid #e53e3e;">
                                        <div style="display: flex; gap: 0.75rem;">
                                            <span style="font-size: 1.25rem;">🔒</span>
                                            <div>
                                                <h4 style="font-size: 0.875rem; font-weight: 600; color: #c53030; margin-bottom: 0.25rem;">Security Notice</h4>
                                                <p style="font-size: 0.875rem; color: #744210; margin: 0;">
                                                    <strong>This is a phishing simulation for security awareness training.</strong> 
                                                    Passwords are stored for analysis purposes only to help identify employees who need additional security training. 
                                                    This data should be handled securely and deleted after training is complete.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1.5rem; padding: 1rem; background-color: #fef5e7; border-radius: 0.5rem; border-left: 3px solid #f6ad55;">
                <div style="display: flex; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">💡</span>
                    <div>
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #744210; margin-bottom: 0.25rem;">Security Note</h4>
                        <p style="font-size: 0.875rem; color: #744210; margin: 0;">
                            These employees submitted credentials during the phishing simulation. This indicates they need additional security awareness training. 
                            <strong>No actual credentials were stored</strong> - this is a simulation for educational purposes only.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($submittedCredentials) && count($submittedCredentials) === 0 && $totalSubmitted === 0)
        <div class="card" style="margin-bottom: 2rem; border-left: 4px solid #c6f6d5;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 2rem;">✅</span>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.25rem; color: #22543d;">No Credentials Submitted</h3>
                    <p style="font-size: 0.875rem; color: #718096; margin: 0;">
                        Great news! No employees have submitted credentials in this campaign yet. This indicates good security awareness.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Interactions --}}
    @if($campaign->interactions->count() > 0)
        <div class="card">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Recent Interactions</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.75rem; text-align: left;">Email</th>
                            <th style="padding: 0.75rem; text-align: left;">Action</th>
                            <th style="padding: 0.75rem; text-align: left;">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->interactions->take(10) as $interaction)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 0.75rem;">{{ $interaction->email }}</td>
                                <td style="padding: 0.75rem;">
                                    <span style="padding: 0.25rem 0.75rem; background-color: #e2e8f0; color: #4a5568; border-radius: 9999px; font-size: 0.875rem; text-transform: capitalize;">
                                        {{ $interaction->action_type }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">{{ $interaction->timestamp->format('M d, Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($campaign->interactions->count() > 10)
                <div style="margin-top: 1rem; text-align: center;">
                    <a href="{{ route('client.campaigns.report', $campaign->id) }}" class="btn btn-secondary">View All Interactions</a>
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .submission-row:hover {
        background-color: #f7fafc;
    }
    .submission-details {
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 500px;
        }
    }
    .expand-icon {
        transition: transform 0.2s;
    }
    .expand-icon.expanded {
        transform: rotate(90deg);
    }
</style>

<script>
    function toggleDetails(index) {
        const detailsRow = document.getElementById('details-' + index);
        const icon = document.getElementById('icon-' + index);
        
        if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
            detailsRow.style.display = 'table-row';
            icon.classList.add('expanded');
        } else {
            detailsRow.style.display = 'none';
            icon.classList.remove('expanded');
        }
    }
    
    function togglePassword(interactionId) {
        const passwordElement = document.getElementById('password-' + interactionId);
        const toggleBtn = document.getElementById('toggle-btn-' + interactionId);
        const password = toggleBtn.getAttribute('data-password');
        
        if (!password) {
            console.error('Password not found for interaction:', interactionId);
            return;
        }
        
        if (passwordElement.textContent.includes('•')) {
            // Show password
            passwordElement.textContent = password;
            passwordElement.style.color = '#c53030';
            toggleBtn.textContent = '🙈 Hide';
        } else {
            // Hide password
            passwordElement.textContent = '•'.repeat(password.length);
            passwordElement.style.color = '#c53030';
            toggleBtn.textContent = '👁️ Show';
        }
    }

    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('#flash-message');
        flashMessages.forEach(function(message) {
            // Add fade-out animation
            message.style.transition = 'opacity 0.5s ease-out';
            
            // Hide after 5 seconds
            setTimeout(function() {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500); // Wait for fade-out animation
            }, 5000);
        });
    });
</script>
@endsection

