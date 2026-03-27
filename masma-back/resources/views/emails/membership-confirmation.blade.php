<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Membership Confirmation - MASMA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #005aa8 0%, #003366 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .member-details {
            background: #f0f7ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #005aa8;
        }
        .member-details p {
            margin: 8px 0;
        }
        .credentials {
            background: #fff9e6;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #d4af37;
        }
        .credentials h3 {
            color: #ed6605;
            margin-top: 0;
        }
        .password-box {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 16px;
            text-align: center;
            letter-spacing: 1px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #ed6605;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .attachments {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .success-badge {
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 {{ $isRenewal ? 'Membership Renewed!' : 'Welcome to MASMA!' }}</h1>
            <p>The Maharashtra Solar Manufacturers Association</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $registration->applicant_name }}</strong>,</p>
            
            <div class="success-badge">
                ✅ Payment Confirmed
            </div>
            
            <p>Thank you for {{ $isRenewal ? 'renewing your membership with' : 'joining' }} The Maharashtra Solar Manufacturers Association (MASMA). We are pleased to confirm your membership and payment has been successfully verified.</p>
            
            <div class="member-details">
                <h3 style="margin-top: 0; color: #005aa8;">📋 Membership Details</h3>
                <p><strong>Member ID:</strong> {{ $memberId }}</p>
                <p><strong>Membership Plan:</strong> {{ $membershipPlan }}</p>
                <p><strong>Registration Date:</strong> {{ $registration->created_at->format('d F Y') }}</p>
                <p><strong>Payment Status:</strong> <span style="color: green;">✓ Verified</span></p>
                @if($isRenewal)
                    <p><strong>Type:</strong> Renewal Membership</p>
                @endif
            </div>
            
            @if($hasPassword)
            <div class="credentials">
                <h3>🔐 Login Credentials</h3>
                <p>You can now access your member portal using the credentials below:</p>
                <p><strong>Email:</strong> {{ $registration->office_email }}</p>
                <p><strong>Password:</strong> <code class="password-box">{{ $password }}</code></p>
                <div class="warning">
                    <strong>⚠️ Important:</strong> Please change your password after first login for security.
                </div>
                <center>
                    <a href="{{ url('https://masma.in/login') }}" class="button">Login to Member Portal</a>
                </center>
            </div>
            @endif
            
            <div class="attachments">
                <strong>📎 Attachments:</strong><br>
                ✓ Membership Certificate (PDF)<br>
                ✓ Payment Receipt (PDF)
            </div>
            
            <p>Your membership benefits include:</p>
            <ul>
                <li>Industry networking opportunities</li>
                <li>Exclusive member events and workshops</li>
                <li>Access to member directory and resources</li>
                <li>Voting rights in association matters</li>
                <li>Updates on industry news and regulations</li>
            </ul>
            
            <p>We look forward to your active participation in our community!</p>
            
            <p>Best regards,<br>
            <strong>The Maharashtra Solar Manufacturers Association</strong><br>
            Email: info@masma.in | Website: www.masma.in</p>
        </div>
        
        <div class="footer">
            <p>This is an automated confirmation. Please keep this email for your records.</p>
            <p>&copy; {{ date('Y') }} Maharashtra Solar Manufacturers Association. All rights reserved.</p>
        </div>
    </div>
</body>
</html>