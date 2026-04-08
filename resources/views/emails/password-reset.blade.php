<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f7fafc;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 2rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="color: #2b6cb0; margin: 0;">Password Reset Request</h1>
        </div>

        <div style="margin-bottom: 2rem;">
            <p>Hello {{ $company->name }},</p>
            <p>You requested to reset your password for your Phishing Simulation Platform account.</p>
            <p>Click the button below to reset your password:</p>
        </div>

        <div style="text-align: center; margin: 2rem 0;">
            <a href="{{ $resetUrl }}" style="background-color: #2b6cb0; color: white; padding: 12px 30px; text-decoration: none; border-radius: 0.375rem; display: inline-block; font-weight: 600;">
                Reset Password
            </a>
        </div>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0; color: #718096; font-size: 0.875rem;">
            <p><strong>Important:</strong> This link will expire in 60 minutes.</p>
            <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
            <p style="margin-top: 1rem;">This is an automated message from the Phishing Simulation Platform.</p>
        </div>
    </div>
</body>
</html>






