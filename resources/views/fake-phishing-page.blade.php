<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Verification Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
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
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
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
        }
        .btn:hover {
            background-color: #0056b3;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🔒 SecurePortal</div>
        </div>
        
        <div class="alert">
            <strong>Security Alert:</strong> We detected unusual activity on your account. Please verify your identity to continue.
        </div>
        
        <form id="phishingForm">
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ $email ?? '' }}" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Verify Account</button>
        </form>
        
        <div class="warning">
            <strong>⚠️ This is a Phishing Simulation!</strong><br>
            This page is part of a security awareness training exercise. 
            No real credentials were collected or stored.
        </div>
        
        <div class="footer">
            <p>This is a simulated phishing page for security training purposes.</p>
            <p>Campaign ID: {{ $campaignId ?? 'N/A' }} | Token: {{ $token ?? 'N/A' }}</p>
        </div>
    </div>

    <script>
        document.getElementById('phishingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Track form submission
            fetch('/api/track/{{ $token }}/submitted', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            })
            .then(response => response.json())
            .then(data => {
                alert('Thank you for your submission. This was a phishing simulation. No real data was collected.');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Thank you for your submission. This was a phishing simulation.');
            });
        });
    </script>
</body>
</html>
