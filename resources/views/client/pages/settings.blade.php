@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">⚙️ Settings</h1>
        <p style="color: #718096; margin-bottom: 1.5rem;">Manage your account settings and preferences.</p>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                {{ session('success') }}
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

        <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 2rem;">
            {{-- Settings Navigation --}}
            <div>
                <div class="card" style="padding: 0; overflow: hidden;">
                    <div style="padding: 1rem; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0;">
                        <h3 style="font-size: 1rem; font-weight: 600; margin: 0;">Settings</h3>
                    </div>
                    <nav style="display: flex; flex-direction: column;">
                        <a href="#notifications" onclick="showSection('notifications'); return false;" class="settings-nav-item active" data-section="notifications">
                            🔔 Notifications
                        </a>
                        <a href="#email" onclick="showSection('email'); return false;" class="settings-nav-item" data-section="email">
                            📧 Email Preferences
                        </a>
                        <a href="#security" onclick="showSection('security'); return false;" class="settings-nav-item" data-section="security">
                            🔒 Security
                        </a>
                       
                        <a href="#general" onclick="showSection('general'); return false;" class="settings-nav-item" data-section="general">
                            ⚙️ General
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Settings Content --}}
            <div>
                {{-- Notifications Section --}}
                <div id="section-notifications" class="settings-section active">
                    <div class="card" style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">🔔 Notification Preferences</h2>
                        <p style="color: #718096; margin-bottom: 1.5rem;">Choose how you want to be notified about campaign activities and updates.</p>

                        <form method="POST" action="{{ route('client.settings.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="notifications">

                            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                <div style="padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div>
                                            <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">Campaign Updates</h4>
                                            <p style="color: #718096; font-size: 0.875rem; margin: 0;">Get notified when campaigns are launched, completed, or updated</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="notify_campaign_updates" value="1" {{ old('notify_campaign_updates', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div style="padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div>
                                            <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">Email Reports</h4>
                                            <p style="color: #718096; font-size: 0.875rem; margin: 0;">Receive weekly email reports with campaign statistics</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="notify_email_reports" value="1" {{ old('notify_email_reports', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div style="padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div>
                                            <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">High-Risk Alerts</h4>
                                            <p style="color: #718096; font-size: 0.875rem; margin: 0;">Get immediate notifications when employees submit credentials</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="notify_high_risk" value="1" {{ old('notify_high_risk', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div style="padding: 1rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <div>
                                            <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem;">Billing Notifications</h4>
                                            <p style="color: #718096; font-size: 0.875rem; margin: 0;">Receive notifications about payments and subscription changes</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" name="notify_billing" value="1" {{ old('notify_billing', true) ? 'checked' : '' }}>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div style="padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem; margin-top: 1rem;">
                                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Webhooks</h4>
                                    <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">Configure webhooks for real-time event notifications</p>
                                    <button type="button" class="btn btn-secondary" disabled>Coming Soon</button>
                                </div>

                                <div style="padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem; margin-top: 1rem; border: 2px solid #e2e8f0;">
                                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">📱 Telegram Notifications</h4>
                                    <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">Receive real-time notifications via Telegram</p>
                                    
                                    <div style="margin-bottom: 1rem;">
                                        <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="checkbox" name="telegram_enabled" value="1" {{ old('telegram_enabled', $company->telegram_enabled ?? false) ? 'checked' : '' }} onchange="toggleTelegramFields(this)">
                                            <span>Enable Telegram Notifications</span>
                                        </label>
                                    </div>

                                    <div id="telegram-fields" style="{{ old('telegram_enabled', $company->telegram_enabled ?? false) ? '' : 'display: none;' }}">
                                        <div class="form-group" style="margin-bottom: 1rem;">
                                            <label class="form-label">Bot Token <span style="color: red;">*</span></label>
                                            <input type="text" name="telegram_bot_token" class="form-control" 
                                                   value="{{ old('telegram_bot_token', $company->telegram_bot_token ?? '') }}" 
                                                   placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                                            <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                                Get your bot token from <a href="https://t.me/BotFather" target="_blank" style="color: #2b6cb0;">@BotFather</a> on Telegram
                                            </small>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 1rem;">
                                            <label class="form-label">Chat ID <span style="color: red;">*</span></label>
                                            <input type="text" name="telegram_chat_id" class="form-control" 
                                                   value="{{ old('telegram_chat_id', $company->telegram_chat_id ?? '') }}" 
                                                   placeholder="-1001234567890">
                                            <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                                Get your chat ID by messaging <a href="https://t.me/userinfobot" target="_blank" style="color: #2b6cb0;">@userinfobot</a> or <a href="https://t.me/getidsbot" target="_blank" style="color: #2b6cb0;">@getidsbot</a>
                                            </small>
                                        </div>

                                        <button type="button" class="btn btn-secondary" onclick="testTelegramConnection()" style="margin-top: 0.5rem;">
                                            Test Connection
                                        </button>
                                        <div id="telegram-test-result" style="margin-top: 0.5rem;"></div>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-primary">Save Notification Settings</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Email Preferences Section --}}
                <div id="section-email" class="settings-section" style="display: none;">
                    <div class="card" style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">📧 Email Preferences</h2>
                        <p style="color: #718096; margin-bottom: 1.5rem;">Configure your email notification preferences.</p>

                        <form method="POST" action="{{ route('client.settings.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="email">

                            <div class="form-group">
                                <label class="form-label">Email Frequency</label>
                                <select name="email_frequency" class="form-control">
                                    <option value="realtime" {{ old('email_frequency', 'realtime') === 'realtime' ? 'selected' : '' }}>Real-time (immediate)</option>
                                    <option value="daily" {{ old('email_frequency') === 'daily' ? 'selected' : '' }}>Daily Digest</option>
                                    <option value="weekly" {{ old('email_frequency') === 'weekly' ? 'selected' : '' }}>Weekly Summary</option>
                                    <option value="never" {{ old('email_frequency') === 'never' ? 'selected' : '' }}>Never</option>
                                </select>
                                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                    Choose how often you want to receive email notifications
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <input type="checkbox" name="email_marketing" value="1" {{ old('email_marketing', false) ? 'checked' : '' }}>
                                    Receive marketing emails and product updates
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <input type="checkbox" name="email_newsletter" value="1" {{ old('email_newsletter', false) ? 'checked' : '' }}>
                                    Subscribe to our security awareness newsletter
                                </label>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-primary">Save Email Preferences</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Security Section --}}
                <div id="section-security" class="settings-section" style="display: none;">
                    <div class="card" style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">🔒 Security Settings</h2>
                        <p style="color: #718096; margin-bottom: 1.5rem;">Manage your account security preferences.</p>

                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <div style="padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Two-Factor Authentication</h4>
                                <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">
                                    @if($company->two_factor_enabled)
                                        <span style="color: #22543d; font-weight: 600;">✓ Enabled</span> - Your account is protected with two-factor authentication.
                                    @else
                                        Add an extra layer of security to your account using Google Authenticator or similar apps.
                                    @endif
                                </p>
                                @if($company->two_factor_enabled)
                                    <form method="POST" action="{{ route('2fa.disable') }}" style="display: inline-block;">
                                        @csrf
                                        <div style="margin-bottom: 1rem;">
                                            <label for="disable_password" class="form-label">Enter your password to disable 2FA:</label>
                                            <input type="password" id="disable_password" name="password" class="form-control" required style="max-width: 300px; display: inline-block; margin-right: 0.5rem;">
                                            <button type="submit" class="btn btn-danger">Disable 2FA</button>
                                        </div>
                                    </form>
                                    @if(session('recovery_codes'))
                                        <div style="background-color: #fff3cd; padding: 1rem; border-radius: 0.375rem; margin-top: 1rem;">
                                            <p style="font-weight: 600; margin-bottom: 0.5rem;">Save these recovery codes:</p>
                                            <div style="font-family: monospace; font-size: 0.875rem;">
                                                @foreach(session('recovery_codes') as $code)
                                                    <div>{{ $code }}</div>
                                                @endforeach
                                            </div>
                                            <p style="font-size: 0.875rem; color: #856404; margin-top: 0.5rem; margin-bottom: 0;">
                                                These codes can be used to access your account if you lose your authenticator device.
                                            </p>
                                        </div>
                                    @endif
                                @else
                                    <a href="{{ route('2fa.setup') }}" class="btn btn-primary">Enable Two-Factor Authentication</a>
                                @endif
                            </div>

                            <div style="padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Active Sessions</h4>
                                <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">View and manage your active login sessions</p>
                                <p style="color: #2b6cb0; font-size: 0.875rem; margin: 0;">
                                    Current session: {{ request()->ip() }} - {{ now()->format('M d, Y H:i') }}
                                </p>
                            </div>

                         
                        </div>
                    </div>
                </div>

                {{-- API Settings Section --}}
                {{-- <div id="section-api" class="settings-section" style="display: none;">
                    <div class="card" style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">🔌 API Settings</h2>
                        <p style="color: #718096; margin-bottom: 1.5rem;">Configure your API access and preferences.</p>

                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <div style="padding: 1.5rem; background-color: #f7fafc; border-radius: 0.5rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">Rate Limiting</h4>
                                <p style="color: #718096; font-size: 0.875rem; margin-bottom: 0.5rem;">Current rate limit: <strong>60 requests per minute</strong></p>
                                <p style="color: #718096; font-size: 0.875rem; margin: 0;">Upgrade your plan for higher rate limits</p>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- General Settings Section --}}
                <div id="section-general" class="settings-section" style="display: none;">
                    <div class="card" style="padding: 1.5rem;">
                        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">⚙️ General Settings</h2>
                        <p style="color: #718096; margin-bottom: 1.5rem;">Manage your general account preferences.</p>

                        <form method="POST" action="{{ route('client.settings.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section" value="general">

                            <div class="form-group">
                                <label class="form-label">Time Zone</label>
                                <select name="timezone" class="form-control">
                                    <option value="UTC" {{ old('timezone', 'UTC') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone') === 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                                    <option value="America/Chicago" {{ old('timezone') === 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                                    <option value="America/Denver" {{ old('timezone') === 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                                    <option value="America/Los_Angeles" {{ old('timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                                    <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>London (GMT)</option>
                                    <option value="Europe/Paris" {{ old('timezone') === 'Europe/Paris' ? 'selected' : '' }}>Paris (CET)</option>
                                    <option value="Asia/Tokyo" {{ old('timezone') === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (JST)</option>
                                </select>
                                <small style="color: #718096; font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                                    Select your timezone for accurate timestamps
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Date Format</label>
                                <select name="date_format" class="form-control">
                                    <option value="Y-m-d" {{ old('date_format', 'Y-m-d') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="m/d/Y" {{ old('date_format') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="d/m/Y" {{ old('date_format') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Language</label>
                                <select name="language" class="form-control">
                                    <option value="en" {{ old('language', 'en') === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ old('language') === 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ old('language') === 'de' ? 'selected' : '' }}>German</option>
                                </select>
                            </div>

                            <div style="margin-top: 1.5rem;">
                                <button type="submit" class="btn btn-primary">Save General Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .settings-nav-item {
        padding: 0.75rem 1rem;
        color: #4a5568;
        text-decoration: none;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    .settings-nav-item:hover {
        background-color: #f7fafc;
        color: #2b6cb0;
    }
    .settings-nav-item.active {
        background-color: #ebf8ff;
        color: #2b6cb0;
        font-weight: 600;
        border-left: 3px solid #2b6cb0;
    }
    .settings-section {
        display: none;
    }
    .settings-section.active {
        display: block;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        border-radius: 26px;
        transition: 0.3s;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider {
        background-color: #2b6cb0;
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
</style>

<script>
    function showSection(section) {
        // Hide all sections
        document.querySelectorAll('.settings-section').forEach(sec => {
            sec.classList.remove('active');
            sec.style.display = 'none';
        });
        
        // Remove active class from all nav items
        document.querySelectorAll('.settings-nav-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Show selected section
        document.getElementById('section-' + section).classList.add('active');
        document.getElementById('section-' + section).style.display = 'block';
        
        // Add active class to selected nav item
        document.querySelector(`[data-section="${section}"]`).classList.add('active');
    }

    function toggleTelegramFields(checkbox) {
        const fields = document.getElementById('telegram-fields');
        if (checkbox.checked) {
            fields.style.display = 'block';
        } else {
            fields.style.display = 'none';
        }
    }

    function testTelegramConnection() {
        const botToken = document.querySelector('input[name="telegram_bot_token"]').value;
        const chatId = document.querySelector('input[name="telegram_chat_id"]').value;
        const resultDiv = document.getElementById('telegram-test-result');

        if (!botToken || !chatId) {
            resultDiv.innerHTML = '<div style="color: #e53e3e; font-size: 0.875rem;">Please enter both Bot Token and Chat ID</div>';
            return;
        }

        resultDiv.innerHTML = '<div style="color: #2b6cb0; font-size: 0.875rem;">Testing connection...</div>';

        fetch('{{ route("client.settings.test-telegram") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                bot_token: botToken,
                chat_id: chatId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<div style="color: #22543d; font-size: 0.875rem; font-weight: 600;">✅ ' + data.message + '</div>';
            } else {
                resultDiv.innerHTML = '<div style="color: #e53e3e; font-size: 0.875rem;">❌ ' + data.message + '</div>';
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '<div style="color: #e53e3e; font-size: 0.875rem;">❌ Connection error. Please try again.</div>';
        });
    }
</script>
@endsection

