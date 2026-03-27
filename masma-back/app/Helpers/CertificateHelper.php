<?php

namespace App\Helpers;

class CertificateHelper
{
   /**
     * Get text positions based on your certificate design
     * Adjust these coordinates to match your background image
     * 
     * How to find correct coordinates:
     * 1. Open your certificate image in an image editor (Photoshop, GIMP, etc.)
     * 2. Note the X and Y coordinates where text should appear
     * 3. Update the values below
     */
    public static function getPositions()
    {
        return [
            // Member Name (large text in center)
            'member_name' => [
                'x' => 595,      // Center X (half of width)
                'y' => 420,      // Distance from top (adjust based on your template)
                'size' => 48,
                'color' => '#005aa8',
            ],
            
            // Membership Type/Plan (below member name)
            'membership_plan' => [
                'x' => 595,
                'y' => 520,
                'size' => 28,
                'color' => '#ed6605',
            ],
            
            // Membership Year
            'year' => [
                'x' => 595,
                'y' => 640,
                'size' => 20,
                'color' => '#333333',
            ],
            
            // Member ID (optional, at bottom)
            'member_id' => [
                'x' => 595,
                'y' => 780,
                'size' => 12,
                'color' => '#666666',
            ],
            
            // Issue Date (bottom right)
            'issue_date' => [
                'x' => 1090,    // Near right edge
                'y' => 790,
                'size' => 10,
                'color' => '#999999',
            ],
        ];
    }
    
    /**
     * Get the text for the certificate
     */
    public static function getCertificateText(Registration $registration)
    {
        $memberId = $registration->member_id ?? $registration->parent_member_id;
        $originalRegistration = self::getOriginalRegistration($registration);
        $startYear = date('Y', strtotime($originalRegistration->created_at));
        $endYear = $startYear + 1;
        
        return [
            'member_name' => strtoupper($registration->applicant_name),
            'membership_plan' => $registration->getRegistrationTypeDisplayAttribute(),
            'year' => "for the year 1st April {$startYear} - 31st March {$endYear}",
            'member_id' => "Member ID: {$memberId}",
            'issue_date' => "Issued: " . now()->format('d F Y'),
        ];
    }
    
    /**
     * Get original registration for renewals
     */
    private static function getOriginalRegistration(Registration $registration)
    {
        if ($registration->member_id) {
            return $registration;
        }
        
        $original = Registration::where('member_id', $registration->parent_member_id)->first();
        return $original ?? $registration;
    }
}