<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Phishing Simulation Platform') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: #2d3748;
            line-height: 1.6;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #2d3748;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            text-decoration: none;
        }

        .navbar-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
        }

        .navbar-nav a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-nav a:hover {
            color: #2b6cb0;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #2b6cb0;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2558a3;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background-color: #cbd5e0;
        }

        .btn-danger {
            background-color: #e53e3e;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c53030;
        }

        .main-content {
            min-height: calc(100vh - 80px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background-color: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #4a5568;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2b6cb0;
            box-shadow: 0 0 0 3px rgba(43, 108, 176, 0.1);
        }

        .text-danger {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            min-width: 220px;
            display: none;
            z-index: 1000;
            border: 1px solid #e2e8f0;
        }

        .dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #4a5568;
            text-decoration: none;
            transition: background-color 0.2s;
            font-size: 0.9375rem;
        }

        .dropdown-item:hover {
            background-color: #f7fafc;
            color: #2b6cb0;
        }

        .dropdown-item button {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 0;
            font-size: inherit;
            color: inherit;
            cursor: pointer;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 0.5rem 0;
        }

        /* Notification Styles */
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #e53e3e;
            color: white;
            border-radius: 50%;
            min-width: 18px;
            height: 18px;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            padding: 0 4px;
            line-height: 1;
        }

        .notification-dropdown {
            right: 0;
            left: auto;
        }

        .notification-item {
            display: block;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f7fafc;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f7fafc;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .navbar-toggle {
                display: block;
            }

            .navbar-nav {
                display: none;
                flex-direction: column;
                width: 100%;
                gap: 0.5rem;
                margin-top: 1rem;
            }

            .navbar-nav.mobile-show {
                display: flex;
            }

            .dropdown-menu {
                position: static;
                width: 100%;
                margin-top: 0.5rem;
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }

            .navbar-container {
                flex-wrap: wrap;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('components.chatbot')
    <nav class="navbar">
        <div class="navbar-container">
            <a href="{{ route('home') }}" class="navbar-brand">
                🛡️ Phishing Simulation Platform
            </a>

            <button class="navbar-toggle" onclick="toggleMobileMenu()" aria-label="Toggle navigation">
                ☰
            </button>

            <ul class="navbar-nav" id="navbarNav">
                @guest('company')
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}" class="btn btn-primary" style="color: white;">Get Started</a></li>
                    <li><a href="{{ route('contact-support') }}">💬 Support</a></li>
                @else
                    @php
                        $user = Auth::guard('company')->user();
                        $role = $user->role ?? 'client';
                    @endphp

                    {{-- Admin Navigation --}}
                    @if($role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'adminCampaignsDropdown')">
                                Campaigns <span style="font-size: 0.75rem;">▼</span>
                            </a>
                            <div class="dropdown-menu" id="adminCampaignsDropdown">
                                <a href="{{ route('client.campaigns.index') }}" class="dropdown-item">📋 All Campaigns</a>
                                <a href="{{ route('client.campaigns.create') }}" class="dropdown-item">➕ Create Campaign</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('client.templates') }}" class="dropdown-item">📧 Email Templates</a>
                                <a href="{{ route('client.phishing-pages') }}" class="dropdown-item">🕵️ Phishing Pages</a>
                                
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'adminCompaniesDropdown')">
                                Companies <span style="font-size: 0.75rem;">▼</span>
                            </a>
                            <div class="dropdown-menu" id="adminCompaniesDropdown">
                                <a href="{{ route('admin.companies.index') }}" class="dropdown-item">📋 All Companies</a>
                                <a href="{{ route('admin.companies.create') }}" class="dropdown-item">➕ Create Company</a>
                                <div class="dropdown-divider"></div>
                                <a href="/api/companies" class="dropdown-item">🔌 Companies API</a>
                                <a href="/api/company/dashboard" class="dropdown-item">📊 Dashboard API</a>
                                <a href="/api/companies/{id}/statistics" class="dropdown-item">📈 Statistics API</a>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'adminManagementDropdown')">
                                Management <span style="font-size: 0.75rem;">▼</span>
                            </a>
                            <div class="dropdown-menu" id="adminManagementDropdown">
                                <a href="{{ route('client.users') }}" class="dropdown-item">👥 Users</a>
                                <a href="{{ route('client.reports') }}" class="dropdown-item">📊 Reports</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('admin.companies.index') }}" class="dropdown-item">🏢 Company Management</a>
                                <a href="/api/companies" class="dropdown-item">🔌 Companies API</a>
                            </div>
                        </li>
                        <li><a href="{{ route('api.documentation') }}">API Docs</a></li>
                    @endif

                    {{-- Client Navigation --}}
                    @if($role === 'client' || $role === 'admin')
                        <li><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'clientCampaignsDropdown')">
                                Campaigns <span style="font-size: 0.75rem;">▼</span>
                            </a>
                            <div class="dropdown-menu" id="clientCampaignsDropdown">
                                <a href="{{ route('client.campaigns.index') }}" class="dropdown-item">📋 All Campaigns</a>
                                <a href="{{ route('client.campaigns.create') }}" class="dropdown-item">➕ Create Campaign</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('client.templates') }}" class="dropdown-item">📧 Email Templates</a>
                                <a href="{{ route('client.phishing-pages') }}" class="dropdown-item">🕵️ Phishing Pages</a>
      
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'clientManagementDropdown')">
                                Management <span style="font-size: 0.75rem;">▼</span>
                            </a>
                            <div class="dropdown-menu" id="clientManagementDropdown">
                                <a href="{{ route('client.users') }}" class="dropdown-item">Users</a>
                                <a href="{{ route('client.reports') }}" class="dropdown-item">Reports</a>
                                @if ($role === 'admin')
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ route('admin.companies.index') }}" class="dropdown-item">Company Management</a>
                                @endif
                            </div>
                        </li>
                    @endif

                    {{-- Developer Navigation --}}
                    @if($role === 'developer')
                        <li><a href="{{ route('api.documentation') }}">API Documentation</a></li>
                        <li><a href="/api/documentation">Swagger UI</a></li>
                    @endif

                    {{-- Contact Support Link --}}
                    <li><a href="{{ route('contact-support') }}">💬 Support</a></li>

                    {{-- Notifications Dropdown --}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'notificationsDropdown')" style="position: relative; display: inline-flex; align-items: center; padding: 0.5rem;">
                            <span style="font-size: 1.25rem; position: relative;">🔔</span>
                            @php
                                // Get unread notifications count (can be extended with database later)
                                $unreadCount = 0;
                                // Example: Count recent campaign activities
                                if (Auth::guard('company')->check()) {
                                    $company = Auth::guard('company')->user();
                                    $recentInteractions = \App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
                                        $q->where('company_id', $company->id);
                                    })->where('action_type', 'submitted')
                                      ->where('created_at', '>=', now()->subHours(24))
                                      ->count();
                                    $unreadCount = min($recentInteractions, 99); // Cap at 99
                                }
                            @endphp
                            @if($unreadCount > 0)
                                <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu notification-dropdown" id="notificationsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <h4 style="font-size: 1rem; font-weight: 600; margin: 0;">Notifications</h4>
                                @if($unreadCount > 0)
                                    <a href="{{ route('client.notifications.mark-all-read') }}" style="font-size: 0.875rem; color: #2b6cb0; text-decoration: none;">Mark all as read</a>
                                @endif
                            </div>
                            <div class="notification-list">
                                @php
                                    $notifications = [];
                                    if (Auth::guard('company')->check()) {
                                        $company = Auth::guard('company')->user();
                                        
                                        // Recent campaign activities
                                        $recentCampaigns = $company->campaigns()->latest()->limit(3)->get();
                                        foreach ($recentCampaigns as $campaign) {
                                            if ($campaign->status === 'active') {
                                                $notifications[] = [
                                                    'type' => 'campaign',
                                                    'title' => 'Campaign Active',
                                                    'message' => "Campaign '{$campaign->type}' is now active",
                                                    'time' => $campaign->updated_at,
                                                    'icon' => '📧',
                                                    'link' => route('client.campaigns.show', $campaign->id)
                                                ];
                                            }
                                        }
                                        
                                        // Recent interactions
                                        $recentInteractions = \App\Models\Interaction::whereHas('campaign', function($q) use ($company) {
                                            $q->where('company_id', $company->id);
                                        })->where('action_type', 'submitted')
                                          ->latest()
                                          ->limit(3)
                                          ->get();
                                        
                                        foreach ($recentInteractions as $interaction) {
                                            $notifications[] = [
                                                'type' => 'interaction',
                                                'title' => 'High-Risk Alert',
                                                'message' => "Employee submitted credentials in campaign",
                                                'time' => $interaction->created_at,
                                                'icon' => '⚠️',
                                                'link' => route('client.campaigns.show', $interaction->campaign_id)
                                            ];
                                        }
                                        
                                        // Sort by time
                                        usort($notifications, function($a, $b) {
                                            return $b['time'] <=> $a['time'];
                                        });
                                        
                                        // Limit to 10
                                        $notifications = array_slice($notifications, 0, 10);
                                    }
                                @endphp
                                
                                @if(count($notifications) > 0)
                                    @foreach($notifications as $notification)
                                        <a href="{{ $notification['link'] }}" class="notification-item">
                                            <div style="display: flex; gap: 0.75rem; padding: 0.75rem 1rem;">
                                                <div style="font-size: 1.5rem; flex-shrink: 0;">{{ $notification['icon'] }}</div>
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="font-weight: 600; font-size: 0.875rem; color: #2d3748; margin-bottom: 0.25rem;">{{ $notification['title'] }}</div>
                                                    <div style="font-size: 0.75rem; color: #718096; margin-bottom: 0.25rem;">{{ $notification['message'] }}</div>
                                                    <div style="font-size: 0.75rem; color: #a0aec0;">{{ $notification['time']->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div style="padding: 2rem 1rem; text-align: center; color: #718096;">
                                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔔</div>
                                        <p style="font-size: 0.875rem; margin: 0;">No notifications</p>
                                    </div>
                                @endif
                            </div>
                            @if(count($notifications) > 0)
                                <div style="padding: 0.75rem 1rem; border-top: 1px solid #e2e8f0; text-align: center;">
                                    <a href="{{ route('client.notifications') }}" style="font-size: 0.875rem; color: #2b6cb0; text-decoration: none;">View all notifications</a>
                                </div>
                            @endif
                        </div>
                    </li>

                    {{-- User Dropdown --}}
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event, 'userDropdown')">
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                <span style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                {{ $user->name }}
                                <span style="font-size: 0.75rem;">▼</span>
                            </span>
                        </a>
                        <div class="dropdown-menu" id="userDropdown">
                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0;">
                                <div style="font-weight: 600; color: #2d3748;">{{ $user->name }}</div>
                                <div style="font-size: 0.875rem; color: #718096;">{{ $user->email }}</div>
                                @if($user->plan)
                                    <div style="font-size: 0.75rem; color: #4a5568; margin-top: 0.25rem;">
                                        Plan: {{ $user->plan->name }}
                                    </div>
                                @endif
                            </div>
                            <a href="{{ route('client.profile') }}" class="dropdown-item">
                                <span style="margin-right: 0.5rem;">👤</span> Profile
                            </a>
                            <a href="{{ route('client.settings') }}" class="dropdown-item">
                                <span style="margin-right: 0.5rem;">⚙️</span> Settings
                            </a>
                            <a href="{{ route('contact-support') }}" class="dropdown-item">
                                <span style="margin-right: 0.5rem;">💬</span> Contact Support
                            </a>
                            @if($role === 'client' || $role === 'admin')
                                <a href="{{ route('client.billing') }}" class="dropdown-item">
                                    <span style="margin-right: 0.5rem;">💳</span> Billing
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">
                                    <span style="margin-right: 0.5rem;">🚪</span> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        function toggleDropdown(event, dropdownId) {
            event.preventDefault();
            event.stopPropagation();
            
            // Close all other dropdowns
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(dropdown => {
                if (dropdown.id !== dropdownId) {
                    dropdown.classList.remove('show');
                }
            });
            
            // Toggle the clicked dropdown
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const nav = document.getElementById('navbarNav');
            nav.classList.toggle('mobile-show');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('navbarNav');
            const toggle = document.querySelector('.navbar-toggle');
            
            if (nav && toggle && !nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('mobile-show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
