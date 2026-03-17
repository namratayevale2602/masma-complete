<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor ID Card - {{ $visitor->visitor_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .id-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        
        .id-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .id-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: white;
            border-radius: 50% 50% 0 0;
        }
        
        .id-logo {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .id-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 0 auto 20px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 50px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .id-content {
            padding: 40px 30px 30px;
        }
        
        .id-field {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .id-icon {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #667eea;
        }
        
        .id-info h4 {
            margin: 0;
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .id-info p {
            margin: 5px 0 0;
            font-size: 16px;
            font-weight: 500;
            color: #343a40;
        }
        
        .qr-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-top: 20px;
        }
        
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 0 auto 15px;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .valid-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            color: #667eea;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }
        
        .print-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        @media print {
            body {
                background: white !important;
                padding: 0;
            }
            
            .print-btn {
                display: none;
            }
            
            .id-card {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="id-card">
        <!-- Header -->
        <div class="id-header">
            <div class="id-logo">
                <i class="fas fa-id-card"></i>
            </div>
            <h1 style="font-size: 24px; font-weight: 600; margin: 0;">Visitor ID Card</h1>
            <p style="opacity: 0.9; margin: 5px 0 0;">{{ config('app.name') }}</p>
        </div>
        
        <!-- Content -->
        <div class="id-content">
            <!-- Photo/Initials -->
            <div class="id-photo">
                {{ substr($visitor->visitor_name, 0, 1) }}
            </div>
            
            <!-- Visitor Details -->
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="id-info">
                    <h4>Full Name</h4>
                    <p>{{ $visitor->visitor_name }}</p>
                </div>
            </div>
            
            @if($visitor->bussiness_name)
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="id-info">
                    <h4>Business</h4>
                    <p>{{ $visitor->bussiness_name }}</p>
                </div>
            </div>
            @endif
            
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="id-info">
                    <h4>Email</h4>
                    <p>{{ $visitor->email }}</p>
                </div>
            </div>
            
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="id-info">
                    <h4>Mobile</h4>
                    <p>{{ $visitor->mobile }}</p>
                </div>
            </div>
            
            @if($visitor->phone)
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="id-info">
                    <h4>Phone</h4>
                    <p>{{ $visitor->phone }}</p>
                </div>
            </div>
            @endif
            
            @if($visitor->whatsapp_no)
            <div class="id-field">
                <div class="id-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="id-info">
                    <h4>WhatsApp</h4>
                    <p>{{ $visitor->whatsapp_no }}</p>
                </div>
            </div>
            @endif
            
            @if($visitor->city || $visitor->town || $visitor->village)
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="id-info">
                    <h4>Address</h4>
                    <p>
                        @if($visitor->city){{ $visitor->city }}, @endif
                        @if($visitor->town){{ $visitor->town }}, @endif
                        @if($visitor->village){{ $visitor->village }}@endif
                    </p>
                </div>
            </div>
            @endif
            
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="id-info">
                    <h4>Registration Date</h4>
                    <p>{{ $visitor->created_at->format('F d, Y') }}</p>
                </div>
            </div>
            
            @if($visitor->remark)
            <div class="id-field">
                <div class="id-icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <div class="id-info">
                    <h4>Remarks</h4>
                    <p>{{ $visitor->remark }}</p>
                </div>
            </div>
            @endif
            
            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="qr-code">
                    @if($qrCodeUrl)
                        <img src="{{ $qrCodeUrl }}" alt="QR Code">
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d;">
                            <i class="fas fa-qrcode" style="font-size: 50px;"></i>
                        </div>
                    @endif
                </div>
                <p style="color: #6c757d; font-size: 14px; margin: 0;">Scan this QR code to verify</p>
                <div class="valid-badge">
                    <i class="fas fa-check-circle"></i> Valid Visitor
                </div>
            </div>
        </div>
        
        <!-- ID Number (small at bottom) -->
        <div style="text-align: center; padding: 15px; background: #f8f9fa; border-top: 1px solid #e9ecef;">
            <p style="margin: 0; font-size: 12px; color: #6c757d;">Visitor ID: <strong>VIS-{{ str_pad($visitor->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
        </div>
    </div>
    
    <!-- Print Button -->
    <div class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i>
    </div>

    <script>
        // Add animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const idCard = document.querySelector('.id-card');
            idCard.style.opacity = '0';
            idCard.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                idCard.style.transition = 'all 0.6s ease';
                idCard.style.opacity = '1';
                idCard.style.transform = 'translateY(0)';
            }, 100);
        });

        // Add copy functionality for contact info
        document.querySelectorAll('.id-info p').forEach(element => {
            element.addEventListener('click', function() {
                const text = this.innerText;
                navigator.clipboard.writeText(text).then(() => {
                    const originalText = this.innerText;
                    this.innerText = 'Copied!';
                    this.style.color = '#28a745';
                    
                    setTimeout(() => {
                        this.innerText = originalText;
                        this.style.color = '#343a40';
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>