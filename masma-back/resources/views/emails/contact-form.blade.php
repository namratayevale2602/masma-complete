<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MASMA Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #009e64;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #009e64;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            padding: 10px;
            background-color: white;
            border-radius: 4px;
            border-left: 4px solid #f2ae00;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        a {
            color: #009e64;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Inquiry</h1>
        <p>MASMA Website Contact Form</p>
    </div>
    
    <div class="content">
        <div class="field">
            <span class="label">Submitted On:</span>
            <div class="value">{{ $timestamp }}</div>
        </div>
        
        <div class="field">
            <span class="label">Full Name:</span>
            <div class="value">{{ $name }}</div>
        </div>
        
        <div class="field">
            <span class="label">Company Name:</span>
            <div class="value">{{ $company }}</div>
        </div>
        
        <div class="field">
            <span class="label">Email Address:</span>
            <div class="value">
                <a href="mailto:{{ $email }}">{{ $email }}</a>
            </div>
        </div>
        
        <div class="field">
            <span class="label">Phone Number:</span>
            <div class="value">
                <a href="tel:{{ $phone }}">{{ $phone }}</a>
            </div>
        </div>
        
        <div class="field">
            <span class="label">Interest:</span>
            <div class="value">{{ $interest }}</div>
        </div>
        
        <div class="field">
            <span class="label">Category:</span>
            <div class="value">{{ $category }}</div>
        </div>
        
        <div class="field">
            <span class="label">Message:</span>
            <div class="value">{{ $message }}</div>
        </div>
        
        <div class="footer">
            <p>This email was sent from the contact form on MASMA website.</p>
            <p>Please respond to this inquiry within 24-48 hours.</p>
        </div>
    </div>
</body>
</html>