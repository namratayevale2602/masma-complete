<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Details - {{ $registration->applicant_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #005aa8;
        }
        .header h1 {
            color: #005aa8;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #005aa8;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #005aa8;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .value {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        .images-section {
            margin-top: 30px;
        }
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .image-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .image-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
            color: #005aa8;
        }
        .image-body {
            padding: 15px;
            text-align: center;
        }
        .image-body img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 4px;
        }
        .image-footer {
            background: #f8f9fa;
            padding: 10px 15px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { background: #fff; }
            .section { break-inside: avoid; }
            .image-grid { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>MASMA Registration Details</h1>
            <p>Registration ID: #{{ $registration->id }} | Submitted: {{ $registration->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <div class="section-title">Personal Information</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Full Name</div>
                    <div class="value">{{ $registration->applicant_name }}</div>
                </div>
                <div class="field">
                    <div class="label">Date of Birth</div>
                    <div class="value">{{ $registration->date_of_birth->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="section">
            <div class="section-title">Contact Information</div>
            <div class="grid-4">
                <div class="field">
                    <div class="label">Mobile</div>
                    <div class="value">{{ $registration->mobile }}</div>
                </div>
                <div class="field">
                    <div class="label">Phone</div>
                    <div class="value">{{ $registration->phone ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">WhatsApp</div>
                    <div class="value">{{ $registration->whatsapp_no ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">Email</div>
                    <div class="value">{{ $registration->office_email }}</div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="section">
            <div class="section-title">Address Information</div>
            <div class="grid-3">
                <div class="field">
                    <div class="label">City</div>
                    <div class="value">{{ $registration->city ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">Town</div>
                    <div class="value">{{ $registration->town ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">Village</div>
                    <div class="value">{{ $registration->village ?: 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="section">
            <div class="section-title">Business Information</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Organization</div>
                    <div class="value">{{ $registration->organization ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">Website</div>
                    <div class="value">{{ $registration->website ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">Organization Type</div>
                    <div class="value">{{ $registration->organization_type_display }}</div>
                </div>
                <div class="field">
                    <div class="label">Business Category</div>
                    <div class="value">{{ $registration->business_category_display }}</div>
                </div>
                <div class="field">
                    <div class="label">Date of Incorporation</div>
                    <div class="value">{{ $registration->date_of_incorporation?->format('d M Y') ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">PAN Number</div>
                    <div class="value">{{ $registration->pan_number ?: 'N/A' }}</div>
                </div>
                <div class="field">
                    <div class="label">GST Number</div>
                    <div class="value">{{ $registration->gst_number ?: 'N/A' }}</div>
                </div>
            </div>
            <div class="field" style="margin-top: 15px;">
                <div class="label">About Service</div>
                <div class="value">{{ $registration->about_service ?: 'N/A' }}</div>
            </div>
        </div>

        <!-- Membership References -->
        <div class="section">
            <div class="section-title">Membership References</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Reference 1</div>
                    <div class="value">{{ $registration->membership_reference_1 }}</div>
                </div>
                <div class="field">
                    <div class="label">Reference 2</div>
                    <div class="value">{{ $registration->membership_reference_2 }}</div>
                </div>
            </div>
        </div>

        <!-- Registration Details -->
        <div class="section">
            <div class="section-title">Registration Details</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Registration Type</div>
                    <div class="value">
                        <span class="badge badge-info">{{ $registration->registration_type_display }}</span>
                    </div>
                </div>
                <div class="field">
                    <div class="label">Registration Amount</div>
                    <div class="value">₹{{ number_format($registration->registration_amount, 2) }}</div>
                </div>
                <div class="field">
                    <div class="label">Declaration</div>
                    <div class="value">
                        <span class="badge {{ $registration->declaration ? 'badge-success' : 'badge-danger' }}">
                            {{ $registration->declaration ? 'Accepted' : 'Not Accepted' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Payment Mode</div>
                    <div class="value">{{ $registration->payment_mode_display }}</div>
                </div>
                <div class="field">
                    <div class="label">Transaction Reference</div>
                    <div class="value">{{ $registration->transaction_reference }}</div>
                </div>
                <div class="field">
                    <div class="label">Payment Status</div>
                    <div class="value">
                        <span class="badge {{ $registration->payment_verified ? 'badge-success' : 'badge-warning' }}">
                            {{ $registration->payment_verified ? 'Verified' : 'Pending Verification' }}
                        </span>
                    </div>
                </div>
                @if($registration->payment_verified && $registration->payment_verified_at)
                <div class="field">
                    <div class="label">Verified At</div>
                    <div class="value">{{ $registration->payment_verified_at->format('d M Y, h:i A') }}</div>
                </div>
                @endif
                @if($registration->payment_remarks)
                <div class="field">
                    <div class="label">Remarks</div>
                    <div class="value">{{ $registration->payment_remarks }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Credentials Status -->
        <div class="section">
            <div class="section-title">Credentials Status</div>
            <div class="grid">
                <div class="field">
                    <div class="label">Credentials Sent</div>
                    <div class="value">
                        <span class="badge {{ $registration->credentials_sent ? 'badge-success' : 'badge-warning' }}">
                            {{ $registration->credentials_sent ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
                @if($registration->credentials_sent && $registration->credentials_sent_at)
                <div class="field">
                    <div class="label">Sent At</div>
                    <div class="value">{{ $registration->credentials_sent_at->format('d M Y, h:i A') }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Images Section -->
        <div class="images-section">
            <div class="section-title">Uploaded Documents</div>
            <div class="image-grid">
                @if($registration->applicant_photo_url)
                <div class="image-card">
                    <div class="image-header">Applicant Photo</div>
                    <div class="image-body">
                        <img src="{{ $registration->applicant_photo_url }}" alt="Applicant Photo">
                    </div>
                    <div class="image-footer">
                        <a href="{{ $registration->applicant_photo_url }}" target="_blank" download>Download</a>
                    </div>
                </div>
                @endif

                @if($registration->visiting_card_url)
                <div class="image-card">
                    <div class="image-header">Visiting Card</div>
                    <div class="image-body">
                        <img src="{{ $registration->visiting_card_url }}" alt="Visiting Card">
                    </div>
                    <div class="image-footer">
                        <a href="{{ $registration->visiting_card_url }}" target="_blank" download>Download</a>
                    </div>
                </div>
                @endif

                @if($registration->payment_screenshot_url)
                <div class="image-card">
                    <div class="image-header">Payment Screenshot</div>
                    <div class="image-body">
                        <img src="{{ $registration->payment_screenshot_url }}" alt="Payment Screenshot">
                    </div>
                    <div class="image-footer">
                        <a href="{{ $registration->payment_screenshot_url }}" target="_blank" download>Download</a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ now()->format('d M Y, h:i A') }} | MASMA Registration System</p>
        </div>
    </div>
</body>
</html>