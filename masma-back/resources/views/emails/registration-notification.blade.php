<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Registration</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #005aa8; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .field { margin-bottom: 10px; }
        .field-label { font-weight: bold; color: #005aa8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Registration Received</h1>
        </div>
        
        <div class="content">
            <h2>Applicant Details:</h2>
            
            <div class="field">
                <span class="field-label">Name:</span> {{ $registration->applicant_name }}
            </div>
            
            <div class="field">
                <span class="field-label">Email:</span> {{ $registration->office_email }}
            </div>
            
            <div class="field">
                <span class="field-label">Mobile:</span> {{ $registration->mobile }}
            </div>
            
            <div class="field">
                <span class="field-label">Organization:</span> {{ $registration->organization ?? 'N/A' }}
            </div>
            
            <div class="field">
                <span class="field-label">Registration Type:</span> {{ $registration->registration_type }}
            </div>
            
            <div class="field">
                <span class="field-label">Amount:</span> ₹{{ $registration->registration_amount }}
            </div>
            
            <div class="field">
                <span class="field-label">Business Category:</span> {{ $registration->business_category ?? 'N/A' }}
            </div>
            
            <div class="field">
                <span class="field-label">Submission Date:</span> {{ $registration->created_at->format('F j, Y g:i A') }}
            </div>
            
            <hr>
            
            <p>
                <strong>Action Required:</strong><br>
                Please verify the payment and update the payment status in the admin panel.
            </p>
            
            <p>
                <a href="{{ url('/admin/registrations/' . $registration->id . '/edit') }}" 
                   style="background: #ed6605; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    View in Admin Panel
                </a>
            </p>
        </div>
    </div>
</body>
</html>