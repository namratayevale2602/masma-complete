{{-- resources/views/pdf/visitor-id-card-simple.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visitor ID Card - {{ $visitor->visitor_name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: 243pt 353pt;
        }
        
        body {
            margin: 0;
            padding: 15pt;
            font-family: Helvetica, Arial, sans-serif;
            width: 243pt;
            height: 370pt;
            background: #669eea;
            color: white;
        }
        
        .card {
            width: 80%;
            height: 80%;
            padding: 10pt;
            box-sizing: border-box;
            border: 1pt solid rgba(255, 255, 255, 0.2);
            border-radius: 6pt;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .header {
            text-align: center;
            margin-bottom: 8pt;
        }
        
        .header h1 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .header p {
            margin: 2pt 0 0 0;
            font-size: 8pt;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }
        
        .content {
            display: flex;
            height: 80pt;
            gap: 8pt;
        }
        
        .left {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .qr-container {
            width: 55pt;
            height: 55pt;
            background: white;
            border-radius: 3pt;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3pt;
            padding: 3pt;
            border: 1pt solid rgba(255, 255, 255, 0.3);
        }
        
        .qr-image {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }
        
        .qr-fallback {
            color: #666;
            font-size: 7pt;
            text-align: center;
            line-height: 1.2;
        }
        
        
        .right {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-top: 10pt;
        }
        
        .name {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 6pt 0;
            text-transform: uppercase;
            text-align: left;
        }
        
        .detail {
            font-size: 7pt;
            margin: 10pt 0;
            display: flex;
        }
        
        .detail-label {
            min-width: 10pt;
            font-weight: bold;
        }
        
        .detail-value {
            flex: 1;
            word-break: break-word;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 6pt;
            margin-top: 100pt;
            padding-top: 4pt;
            border-top: 0.5pt solid rgba(255,255,255,0.3);
        }
        
        .id-number {
            font-weight: bold;
        }
        
        .date {
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <p>VISITOR IDENTIFICATION CARD</p>
        </div>
        
        <div class="content">
            <div class="left">
                <div class="qr-container">
                    @if(isset($qrCodeImage) && $qrCodeImage)
                        <img src="data:image/png;base64,{{ $qrCodeImage }}" 
                             alt="QR Code for {{ $visitor->visitor_name }}"
                             class="qr-image">
                    @else
                        <div class="qr-fallback">
                            QR CODE<br>
                            NOT AVAILABLE
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="right">
                <div class="name">{{ $visitor->visitor_name }}</div>
                
                <div class="detail">
                    <div class="detail-label">PHONE:</div>
                    <div class="detail-value">{{ $visitor->mobile }}</div>
                </div>
                
                <div class="detail">
                    <div class="detail-label">EMAIL:</div>
                    <div class="detail-value">{{ \Illuminate\Support\Str::limit($visitor->email, 25) }}</div>
                </div>
                
                @if($visitor->bussiness_name)
                <div class="detail">
                    <div class="detail-label">COMPANY:</div>
                    <div class="detail-value">{{ \Illuminate\Support\Str::limit($visitor->bussiness_name, 22) }}</div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="footer">
            <div class="id-number">
                ID: #{{ str_pad($visitor->id, 6, '0', STR_PAD_LEFT) }}
            </div>
            <div class="date">
                ISSUED: {{ $visitor->created_at->format('M d, Y') }}
            </div>
        </div>
    </div>
</body>
</html>