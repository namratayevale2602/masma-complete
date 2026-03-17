<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $isPasswordReset ? 'Password Reset - ' : 'Your Account Credentials - ' }}{{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #005aa8; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
        .credentials { background: white; padding: 15px; border-left: 4px solid #ed6605; margin: 15px 0; border-radius: 4px; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px; margin: 15px 0; }
        .login-btn { background: #ed6605; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $isPasswordReset ? 'Password Reset' : 'Welcome to ' . config('app.name') }}</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $registration->applicant_name }}</strong>,</p>
            
            @if($isPasswordReset)
                <p>Your password has been successfully reset as requested. Here are your new login credentials:</p>
            @else
                <p>Your registration has been verified and your payment has been confirmed. We're pleased to inform you that your account has been created successfully.</p>
            @endif
            
            <div class="credentials">
                <h3 style="color: #005aa8; margin-top: 0;">Your Login Details:</h3>
                <p><strong>Username/Email:</strong> {{ $registration->office_email }}</p>
                <p><strong>Password:</strong> <code style="background: #f4f4f4; padding: 4px 8px; border-radius: 3px; font-size: 16px;">{{ $password }}</code></p>
                <p><strong>Login URL:</strong> <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
                
                <a href="{{ url('/login') }}" class="login-btn">Login to Your Account</a>
            </div>
            
            <div class="warning">
                <strong>🔒 Important Security Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Keep your credentials secure and confidential</li>
                    <li>Change your password after first login for security</li>
                    <li>Do not share your password with anyone</li>
                    <li>This password will be used to encrypt your sensitive data</li>
                    @if($isPasswordReset)
                        <li>Your old password is no longer valid</li>
                    @endif
                    <li>If you didn't request this, please contact us immediately</li>
                </ul>
            </div>
            
            <p>If you have any questions or need assistance with your account, please don't hesitate to contact our support team.</p>
            
            <p>Best regards,<br>
            <strong>The {{ config('app.name') }} Team</strong></p>
        </div>
    </div>
</body>
</html>