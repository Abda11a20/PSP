<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->name ?? 'Account Verification Required' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            display: none;
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .campaign-info {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔒 {{ $campaign->type === 'phishing' ? 'SecurePortal' : ($campaign->type === 'awareness' ? 'SecurityHub' : 'TrainingPortal') }}</div>
        </div>
        
        @if($campaign->type === 'phishing')
            <div class="alert alert-danger">
                <strong>Security Alert:</strong> We detected unusual activity on your account. Please verify your identity to continue.
            </div>
        @elseif($campaign->type === 'awareness')
            <div class="alert">
                <strong>Security Training:</strong> Complete this security awareness exercise to help protect your organization.
            </div>
        @else
            <div class="alert alert-success">
                <strong>Training Module:</strong> Please complete this security training module.
            </div>
        @endif
        
        <div class="campaign-info">
            <strong>Campaign:</strong> {{ $campaign->type }} | <strong>Target:</strong> {{ $target->name }} ({{ $target->email }})
        </div>
        
        <form id="phishingForm">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ $target->email }}" required readonly>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            @if($campaign->type === 'awareness' || $campaign->type === 'training')
                <div class="form-group">
                    <label for="department">Department:</label>
                    <input type="text" id="department" name="department" placeholder="Enter your department" required>
                </div>
            @endif
            
            <button type="submit" class="btn" id="submitBtn">
                {{ $campaign->type === 'phishing' ? 'Verify Account' : ($campaign->type === 'awareness' ? 'Complete Training' : 'Submit Training') }}
            </button>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing...</p>
            </div>
        </form>
        
        <div class="success-message" id="successMessage">
            <strong>✅ Thank you for your submission!</strong><br>
            This was a phishing simulation. No real credentials were collected or stored.
        </div>
        
        <div class="warning">
            <strong>⚠️ This is a Phishing Simulation!</strong><br>
            This page is part of a security awareness training exercise. 
            No real credentials were collected or stored.
        </div>
        
        <div class="footer">
            <p>This is a simulated phishing page for security training purposes.</p>
            <p>Campaign ID: {{ $campaignId }} | Token: {{ $token }}</p>
            <p>Template: {{ $template->name }}</p>
        </div>
    </div>

    <script>
        document.getElementById('phishingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const successMessage = document.getElementById('successMessage');
            const form = document.getElementById('phishingForm');
            
            // Show loading state
            submitBtn.disabled = true;
            loading.style.display = 'block';
            form.style.display = 'none';
            
            // Prepare form data (capture password for simulation analysis)
            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value, // Capture for simulation analysis
                department: document.getElementById('department')?.value || '',
                timestamp: new Date().toISOString(),
                campaign_type: '{{ $campaign->type }}',
                template_name: '{{ $template->name }}'
            };
            
            // Track form submission
            fetch('/api/campaign/{{ $token }}/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading
                loading.style.display = 'none';
                
                if (data.success) {
                    // Show success message
                    successMessage.style.display = 'block';
                } else {
                    // Show error message
                    alert('Error: ' + (data.message || 'Submission failed'));
                    // Reset form
                    submitBtn.disabled = false;
                    form.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loading.style.display = 'none';
                alert('Thank you for your submission. This was a phishing simulation.');
                successMessage.style.display = 'block';
            });
        });
        
        // Track page view
        window.addEventListener('load', function() {
            // Track that the page was viewed
            fetch('/api/track/{{ $token }}/clicked', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(error => {
                console.log('Tracking error (non-critical):', error);
            });
        });
    </script>
</body>
</html>
