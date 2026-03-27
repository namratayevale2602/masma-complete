<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $receipt_number }}</title>
    <style>
        @page {
            margin: 20px;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #005aa8;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #005aa8;
            margin: 5px 0;
        }
        
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #ed6605;
            margin: 20px 0;
            text-align: center;
        }
        
        .receipt-number {
            text-align: right;
            font-size: 12px;
            color: #999;
            margin-bottom: 20px;
        }
        
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #005aa8;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .info-value {
            flex: 1;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .table th {
            background-color: #005aa8;
            color: white;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .status-paid {
            color: green;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-line {
            text-align: center;
            width: 200px;
        }
        
        .signature-line hr {
            margin: 10px 0;
            width: 180px;
        }
        
        .verification-note {
            background: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="MASMA Logo">
            @endif
            <div class="title">MAHARASHTRA SOLAR MANUFACTURERS ASSOCIATION</div>
            <div class="subtitle">(MASMA)</div>
        </div>
        
        <div class="receipt-title">PAYMENT RECEIPT</div>
        
        <div class="receipt-number">
            Receipt No: {{ $receipt_number }}<br>
            Date: {{ $payment_date }}
        </div>
        
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Member Name:</div>
                <div class="info-value">{{ $member_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Member ID:</div>
                <div class="info-value">{{ $member_id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Registration Type:</div>
                <div class="info-value">{{ $registration_type }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Membership Plan:</div>
                <div class="info-value">{{ $membership_plan }}</div>
            </div>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $registration_type }} - {{ $membership_plan }}</td>
                    <td class="amount">{{ number_format($amount, 2) }}</td>
                </tr>
                <tr>
                    <td>GST (18%)</td>
                    <td class="amount">{{ number_format($gst_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Amount</strong></td>
                    <td class="amount"><strong>₹ {{ number_format($total_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Payment Mode:</div>
                <div class="info-value">{{ $payment_mode }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Transaction Reference:</div>
                <div class="info-value">{{ $transaction_reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">{{ $payment_date }} at {{ $payment_time }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Status:</div>
                <div class="info-value"><span class="status-paid">{{ $status }}</span></div>
            </div>
        </div>
        
        <div class="verification-note">
            ✅ Payment Verified by: {{ $verified_by }} on {{ $verified_at }}
        </div>
        
        <div class="signature">
            <div class="signature-line">
                <hr>
                <small>Authorized Signatory</small>
            </div>
            <div class="signature-line">
                <hr>
                <small>Member's Signature</small>
            </div>
        </div>
        
        <div class="footer">
            <p>This is a computer-generated receipt and does not require a physical signature.</p>
            <p>For any queries, please contact: info@masma.in | www.masma.in</p>
        </div>
    </div>
</body>
</html>