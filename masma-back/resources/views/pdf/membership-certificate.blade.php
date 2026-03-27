<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Membership Certificate - {{ $member_name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            background: #f5f5f5;
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            min-height: 297mm; /* A4 landscape height */
            background: white;
            position: relative;
            padding: 20px;
            box-sizing: border-box;
        }
        
        /* Main Border */
        .certificate-border {
            border: 12px solid #d4af37;
            padding: 20px;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #fff9e6 100%);
        }
        
        /* Decorative Border Pattern */
        .border-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 1px solid #d4af37;
            pointer-events: none;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }
        
        .title {
            font-size: 42px;
            font-weight: bold;
            color: #d4af37;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .subtitle {
            font-size: 18px;
            color: #005aa8;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .org-name {
            font-size: 20px;
            color: #333;
            margin-top: 5px;
            font-weight: bold;
        }
        
        /* Content Section */
        .content {
            text-align: center;
            margin: 30px 0;
        }
        
        .certify-text {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .member-name {
            font-size: 48px;
            font-weight: bold;
            color: #005aa8;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .membership-text {
            font-size: 22px;
            color: #333;
            margin: 15px 0;
        }
        
        .membership-plan {
            font-size: 28px;
            font-weight: bold;
            color: #ed6605;
            margin: 10px 0;
            padding: 5px 20px;
            background: #fff0e6;
            display: inline-block;
            border-radius: 10px;
        }
        
        .date-range {
            font-size: 20px;
            color: #666;
            margin-top: 20px;
            font-style: italic;
        }
        
        .renewal-badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
        
        /* Member Details */
        .member-details {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            display: inline-block;
            text-align: left;
        }
        
        .member-details p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        /* Footer Section */
        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            margin-top: 30px;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .signature-line {
            text-align: center;
            width: 200px;
        }
        
        .signature-line hr {
            width: 180px;
            margin: 10px 0;
            border: 1px solid #333;
        }
        
        .signature-text {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        
        .certificate-number {
            font-size: 10px;
            color: #999;
            margin-top: 15px;
        }
        
        /* Seal */
        .seal {
            position: absolute;
            bottom: 60px;
            right: 60px;
            width: 100px;
            height: 100px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            text-align: center;
            padding: 20px 5px;
            background: rgba(212, 175, 55, 0.1);
        }
        
        .seal p {
            margin: 0;
            font-weight: bold;
            font-size: 12px;
            color: #d4af37;
        }
        
        .seal .seal-text {
            font-size: 16px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-border">
            <div class="border-pattern"></div>
            
            <!-- Header -->
            <div class="header">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="MASMA Logo">
                @endif
                <h1 class="title">Membership Certificate</h1>
                <p class="subtitle">THE MAHARASHTRA SOLAR MANUFACTURERS ASSOCIATION</p>
                <p class="org-name">(MASMA)</p>
            </div>
            
            <!-- Content -->
            <div class="content">
                <p class="certify-text">This is to certify that</p>
                <h2 class="member-name">{{ $member_name }}</h2>
                <p class="membership-text">is a member of</p>
                <p class="membership-text">The Maharashtra Solar Manufacturers Association</p>
                <div class="membership-plan">{{ $membership_plan }}</div>
                
                @if($is_renewal)
                    <div class="renewal-badge">
                        ★ Renewal Member ({{ $renewal_count }}th Renewal) ★
                    </div>
                @endif
                
                <p class="date-range">for the year {{ $start_date }} - {{ $end_date }}</p>
                
                <div class="member-details">
                    <p><strong>Member ID:</strong> {{ $member_id }}</p>
                    <p><strong>Certificate No:</strong> {{ $certificate_number }}</p>
                    <p><strong>Issue Date:</strong> {{ $issue_date }}</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <div class="signatures">
                    <div class="signature-line">
                        <hr>
                        <p class="signature-text">(President)</p>
                        <p class="signature-text">MASMA</p>
                    </div>
                    <div class="signature-line">
                        <hr>
                        <p class="signature-text">(Secretary)</p>
                        <p class="signature-text">MASMA</p>
                    </div>
                </div>
                
                <div class="certificate-number">
                    This certificate is electronically generated and valid without signature
                </div>
            </div>
            
            <!-- Seal -->
            <div class="seal">
                <p class="seal-text">MASMA</p>
                <p>OFFICIAL</p>
                <p>SEAL</p>
            </div>
        </div>
    </div>
</body>
</html>