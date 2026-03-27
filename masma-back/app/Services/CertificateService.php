<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Models\Registration;
use App\Helpers\CertificateHelper;

class CertificateService
{
    /**
     * Generate membership certificate with background image
     */
    public function generateCertificate(Registration $registration): string
    {
        // Get the original member ID
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        
        // Determine membership year
        $originalRegistration = $this->getOriginalRegistration($registration);
        $startYear = date('Y', strtotime($originalRegistration->created_at));
        $endYear = $startYear + 1;
        
        // Load background certificate template
        $backgroundPath = storage_path('app/public/certificates/templates/masma_certificate_blank.png');
        
        // If background doesn't exist, create a default one
        if (!file_exists($backgroundPath)) {
            $this->createDefaultBackground();
            $backgroundPath = storage_path('app/public/certificates/templates/masma_certificate_blank.png');
        }
        
        // Create image from background
        $img = Image::make($backgroundPath);
        
        // Get image dimensions
        $width = $img->width();
        $height = $img->height();
        $centerX = $width / 2;
        
        // Define font paths
        $boldFont = $this->getFontPath('bold');
        $regularFont = $this->getFontPath('regular');
        
        // Colors
        $primaryColor = '#005aa8';      // Blue for member name
        $secondaryColor = '#ed6605';    // Orange for membership type
        $textColor = '#333333';         // Dark gray for year
        $goldColor = '#d4af37';         // Gold for decorative text
        
        // ==================== TEXT POSITIONING ====================
        // Based on your certificate blank image layout
        
        // 1. Member Name - Should appear in the large blank space (center)
        // Coordinates: (centerX, Y position from top)
        $memberName = strtoupper($registration->applicant_name);
        $img->text($memberName, $centerX, 390, function($font) use ($boldFont) {
            $font->file($boldFont);
            $font->size(40);
            $font->color('#005aa8');
            $font->align('center');
            $font->valign('middle');
        });
        
        // 2. Membership Type/Plan - Below member name
        $membershipType = $registration->getRegistrationTypeDisplayAttribute();
        $img->text($membershipType, $centerX, 440, function($font) use ($regularFont, $secondaryColor) {
            $font->file($regularFont);
            $font->size(28);
            $font->color($secondaryColor);
            $font->align('center');
            $font->valign('middle');
        });

        // 3. Membership Year - At the bottom of the blank space
        $yearText = "is member of The Maharashtra Solar Manufacturers Association";
        $img->text($yearText, $centerX, 480, function($font) use ($regularFont, $textColor) {
            $font->file($regularFont);
            $font->size(20);
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
        });
        
        // 3. Membership Year - At the bottom of the blank space
        $yearText = "for the year 1st April {$startYear} - 31st March {$endYear}";
        $img->text($yearText, $centerX, 520, function($font) use ($regularFont, $textColor) {
            $font->file($regularFont);
            $font->size(20);
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
        });
        
        // 4. Member ID - Small text at bottom (optional)
        // $memberIdText = "Member ID: {$memberId}";
        // $img->text($memberIdText, $centerX, 780, function($font) use ($regularFont) {
        //     $font->file($regularFont);
        //     $font->size(12);
        //     $font->color('#666666');
        //     $font->align('center');
        //     $font->valign('middle');
        // });
        
        // // 5. Issue Date - Bottom right corner (optional)
        // $issueDate = now()->format('d F Y');
        // $img->text("Issued: {$issueDate}", $width - 100, $height - 50, function($font) use ($regularFont) {
        //     $font->file($regularFont);
        //     $font->size(10);
        //     $font->color('#999999');
        //     $font->align('right');
        //     $font->valign('bottom');
        // });
        
        // Save the generated certificate
        $filename = "certificates/certificate_{$registration->id}_{$memberId}.png";
        $fullPath = storage_path("app/public/{$filename}");
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save image with high quality
        $img->save($fullPath, 95);
        
        return $fullPath;
    }
    
    /**
     * Get font path based on type
     */
    private function getFontPath($type = 'regular')
    {
        $fontPaths = [
            'regular' => [
                storage_path('app/public/fonts/arial.ttf'),
                public_path('fonts/arial.ttf'),
                public_path('fonts/Roboto-Regular.ttf'),
                'C:/Windows/Fonts/arial.ttf', // Windows
                '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf', // Linux
                '/System/Library/Fonts/Helvetica.ttc', // macOS
            ],
            'bold' => [
                storage_path('app/public/fonts/arialbd.ttf'),
                public_path('fonts/arialbd.ttf'),
                public_path('fonts/Roboto-Bold.ttf'),
                'C:/Windows/Fonts/arialbd.ttf', // Windows
                '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf', // Linux
                '/System/Library/Fonts/Helvetica-Bold.ttc', // macOS
            ],
        ];
        
        foreach ($fontPaths[$type] as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        return null;
    }
    
    /**
     * Create default background if none exists
     */
    private function createDefaultBackground()
    {
        $width = 1190;
        $height = 842;
        
        $img = Image::canvas($width, $height, '#ffffff');
        
        // Add decorative border
        $gold = '#d4af37';
        $img->rectangle(20, 20, $width - 20, $height - 20, function($draw) use ($gold) {
            $draw->border(3, $gold);
        });
        
        // Add inner border
        $img->rectangle(40, 40, $width - 40, $height - 40, function($draw) use ($gold) {
            $draw->border(1, $gold);
        });
        
        // Header
        $img->text('The Maharashtra Solar Manufacturers Association', $width / 2, 80, function($font) {
            $font->size(28);
            $font->color('#005aa8');
            $font->align('center');
            $font->valign('middle');
        });
        
        $img->text('(MASMA)', $width / 2, 120, function($font) {
            $font->size(18);
            $font->color('#666666');
            $font->align('center');
            $font->valign('middle');
        });
        
        $img->text('CERTIFICATE OF MEMBERSHIP', $width / 2, 200, function($font) {
            $font->size(32);
            $font->color('#d4af37');
            $font->align('center');
            $font->valign('middle');
        });
        
        $img->text('This is to certify that', $width / 2, 300, function($font) {
            $font->size(24);
            $font->color('#333333');
            $font->align('center');
            $font->valign('middle');
        });
        
        // Save template
        $templatePath = storage_path('app/public/certificates/templates/masma_certificate_blank.png');
        $directory = dirname($templatePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $img->save($templatePath);
    }
    
    /**
     * Generate payment receipt
     */
    /**
 * Generate payment receipt
 */
public function generatePaymentReceipt(Registration $registration): string
{
    $memberId = $registration->member_id ?? $registration->parent_member_id;
    
    $width = 900;
    $height = 1000;
    
    $img = Image::canvas($width, $height, '#ffffff');
    
    // Define colors
    $darkBlue = '#003366';
    $lightBlue = '#005aa8';
    $orange = '#ed6605';
    $darkGray = '#000000';
    $lightGray = '#666666';
    $borderGray = '#dddddd';
    
    // Load logo if exists
    $logoPath = storage_path('app/public/logo/masma.png');
    if (file_exists($logoPath)) {
        try {
            $logo = Image::make($logoPath);
            $logo->resize(80, 80);
            $img->insert($logo, 'top-left', 40, 30);
        } catch (\Exception $e) {
            // Logo not found, continue without logo
        }
    }
    
    // Header Section
    $yOffset = 30;
    
    // Association Name
    $img->text('The Maharashtra Solar Manufacturers Association', $width / 2, $yOffset + 10, function($font) use ($orange) {
        $font->file($this->getFontPath('bold'));
        $font->size(22);
        $font->color($orange);
        $font->align('center');
        $font->valign('middle');
    });
    
    // Address and Contact - Single row as per image
    $addressText = "H.O.: D-93, 4th Floor, K K Market, Near Shankar Maharaj Math, Pune Satara Road, Dankawadi, Pune 411043.";
    $contactText = "Mob.: 89993 16256, 93091 67947 | Email: info@masma.in | Web: www.masma.in";
    
    $img->text($addressText, $width / 2, $yOffset + 45, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(12);
        $font->color($darkGray);
        $font->align('center');
        $font->valign('middle');
    });
    
    $img->text($contactText, $width / 2, $yOffset + 65, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(12);
        $font->color($darkGray);
        $font->align('center');
        $font->valign('middle');
    });
    
    // Decorative line after header
    // $this->drawThickLine($img, 50, $yOffset + 85, $width - 50, $yOffset + 85, 1, $borderGray);
    
    // Receipt Title
    $img->text('PAYMENT RECEIPT', $width / 2, $yOffset + 125, function($font) use ($darkBlue) {
        $font->file($this->getFontPath('bold'));
        $font->size(28);
        $font->color($darkBlue);
        $font->align('center');
        $font->valign('middle');
    });
    
    // Receipt Number
    $receiptNumber = "Receipt No: MASMA/RCPT/" . date('Y') . "/" . str_pad($registration->id, 5, '0', STR_PAD_LEFT);
    $img->text($receiptNumber, $width - 30, $yOffset + 155, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(10);
        $font->color($darkGray);
        $font->align('right');
        $font->valign('middle');
    });
    
    // Date
    $dateText = "Date: " . now()->format('d F Y');
    $img->text($dateText, $width - 30, $yOffset + 175, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(10);
        $font->color($darkGray);
        $font->align('right');
        $font->valign('middle');
    });
    
    // Member Details Section
    $yPos = $yOffset + 220;
    
    // Section Header
    $img->text('MEMBER DETAILS', 50, $yPos, function($font) use ($darkBlue) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color($darkBlue);
        $font->align('left');
        $font->valign('middle');
    });
    
    // Decorative line under section header
    // $this->drawThickLine($img, 50, $yPos + 10, $width - 50, $yPos + 10, 1, $borderGray);
    
    $yPos += 50;
    
    // Member Details
    $details = [
        'Member Name' => strtoupper($registration->applicant_name),
        'Member ID' => $memberId,
        'Membership Plan' => $registration->getRegistrationTypeDisplayAttribute(),
        'Registration Type' => $registration->isRenewal() ? 'Membership Renewal' : 'New Membership',
    ];
    
    foreach ($details as $label => $value) {
        $img->text($label . ' : ', 50, $yPos, function($font) use ($darkGray) {
            $font->file($this->getFontPath('regular'));
            $font->size(14);
            $font->color($darkGray);
            $font->align('left');
        });
        
        $img->text($value, 200, $yPos, function($font) use ($lightBlue) {
            $font->file($this->getFontPath('regular'));
            $font->size(14);
            $font->color($lightBlue);
            $font->align('left');
        });
        
        $yPos += 28;
    }
    
    $yPos += 15;
    
    // Payment Details Section
    $img->text('PAYMENT DETAILS', 50, $yPos, function($font) use ($darkBlue) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color($darkBlue);
        $font->align('left');
        $font->valign('middle');
    });
    
    // Decorative line under section header
    // $this->drawThickLine($img, 50, $yPos + 10, $width - 50, $yPos + 10, 1, $borderGray);
    
    $yPos += 35;
    
    // Payment Table
    $tableTop = $yPos;
    $tableLeft = 50;
    $tableRight = $width - 50;
    
    // Table Header
    $img->rectangle($tableLeft, $tableTop, $tableRight, $tableTop + 35, function($draw) use ($lightBlue) {
        $draw->background($lightBlue);
    });
    
    $img->text('Description', $tableLeft + 15, $tableTop + 22, function($font) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color('#ffffff');
        $font->align('left');
    });
    
    $img->text('Amount (₹)', $tableRight - 100, $tableTop + 22, function($font) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color('#ffffff');
        $font->align('right');
    });
    
    $yPos = $tableTop + 35;
    
    // Table Row - Membership Fee
    $img->rectangle($tableLeft, $yPos, $tableRight, $yPos + 40, function($draw) use ($borderGray) {
        $draw->border(1, $borderGray);
    });
    
    $membershipFeeText = "Membership Fee - " . $registration->getRegistrationTypeDisplayAttribute();
    $img->text($membershipFeeText, $tableLeft + 15, $yPos + 25, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(14);
        $font->color($darkGray);
        $font->align('left');
    });
    
    $img->text('₹ ' . number_format($registration->registration_amount, 2), $tableRight - 15, $yPos + 25, function($font) use ($darkGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(14);
        $font->color($darkGray);
        $font->align('right');
    });
    
    $yPos += 40;
    
    // Total Row
    $img->rectangle($tableLeft, $yPos, $tableRight, $yPos + 40, function($draw) {
        $draw->background('#f9f9f9');
        $draw->border(1, '#dddddd');
    });
    
    $img->text('TOTAL', $tableRight - 150, $yPos + 25, function($font) use ($orange) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color($orange);
        $font->align('right');
    });
    
    $img->text('₹ ' . number_format($registration->registration_amount, 2), $tableRight - 15, $yPos + 25, function($font) use ($orange) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color($orange);
        $font->align('right');
    });
    
    $yPos += 75;
    
    // Payment Information
    $paymentInfo = [
        'Payment Mode' => strtoupper($registration->payment_mode),
        'Transaction Reference' => $registration->transaction_reference,
        'Payment Date' => $registration->created_at->format('d F Y, h:i A'),
        'Payment Status' => 'PAID & VERIFIED',
    ];
    
    foreach ($paymentInfo as $label => $value) {
        $img->text($label . ' :', 50, $yPos, function($font) use ($darkGray) {
            $font->file($this->getFontPath('regular'));
            $font->size(14);
            $font->color($darkGray);
            $font->align('left');
        });
        
        $color = ($label == 'Payment Status') ? '#28a745' : $lightBlue;
        $img->text($value, 220, $yPos, function($font) use ($color) {
            $font->file($this->getFontPath('regular'));
            $font->size(14);
            $font->color($color);
            $font->align('left');
        });
        
        $yPos += 25;
    }
    
    $yPos += 20;
    
    // Footer Notes
    $img->text('This is a computer-generated receipt and does not require a physical signature.', $width / 2, $height - 70, function($font) use ($lightGray) {
        $font->file($this->getFontPath('regular'));
        $font->size(14);
        $font->color($lightGray);
        $font->align('center');
        $font->valign('middle');
    });
    
    $img->text('Thank you for being a member of MASMA!', $width / 2, $height - 45, function($font) use ($lightBlue) {
        $font->file($this->getFontPath('bold'));
        $font->size(14);
        $font->color($lightBlue);
        $font->align('center');
        $font->valign('middle');
    });
    
    // Save receipt
    $filename = "receipts/receipt_{$registration->id}_{$memberId}.png";
    $fullPath = storage_path("app/public/{$filename}");
    
    $directory = dirname($fullPath);
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $img->save($fullPath, 95);
    
    return $fullPath;
}
    
    /**
     * Get original registration for renewals
     */
    private function getOriginalRegistration(Registration $registration): Registration
    {
        if ($registration->member_id) {
            return $registration;
        }
        
        $original = Registration::where('member_id', $registration->parent_member_id)->first();
        return $original ?? $registration;
    }
}