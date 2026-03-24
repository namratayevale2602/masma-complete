<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class Registration extends Model
{
    protected $fillable = [
        'applicant_name',
        'date_of_birth',
        'organization',
        'mobile',
        'phone',
        'whatsapp_no',
        'office_email',
        'city',
        'town',
        'village',
        'website',
        'organization_type',
        'business_category',
        'date_of_incorporation',
        'pan_number',
        'gst_number',
        'about_service',
        'membership_reference_1',
        'membership_reference_2',
        'registration_type',
        'registration_amount',
        'payment_mode',
        'transaction_reference',
        'declaration',
        'applicant_photo_path',
        'visiting_card_path',
        'payment_screenshot_path',
        'payment_verified',
        'payment_verified_at',
        'payment_remarks',
        'generated_password',
        'remember_token',
        'credentials_sent',
        'credentials_sent_at',
        'submission_token', // Add this field for duplicate prevention
        'ip_address', // Optional: track IP for security
        'member_id', // Add member_id to fillable
        'parent_member_id', // Add parent_member_id to fillable
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_incorporation' => 'date',
        'declaration' => 'boolean',
        'payment_verified' => 'boolean',
        'credentials_sent' => 'boolean',
        'registration_amount' => 'decimal:2',
        'credentials_sent_at' => 'datetime',
        'payment_verified_at' => 'datetime',
    ];

    /**
     * Generate a unique member ID
     * Format: MASMA-YYYY-XXXXX (where XXXXX is a sequential number)
     */
    public static function generateMemberId(): string
    {
        $year = date('Y');
        $prefix = "MASMA-{$year}-";
        
        // Get the last member ID for this year
        $lastMember = self::where('member_id', 'like', $prefix . '%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember) {
            // Extract the numeric part
            $lastNumber = (int) substr($lastMember->member_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Pad with zeros to 5 digits
        $paddedNumber = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        
        return $prefix . $paddedNumber;
    }
    
    /**
     * Check if registration type is a renewal
     */
    public static function isRenewalType($registrationType): bool
    {
        $renewalTypes = [
            'renew_epc_classic',
            'renew_student',
            'renew_dealer_distributor',
            'renew_silver_corporate',
            'renew_gold_corporate'
        ];
        
        return in_array($registrationType, $renewalTypes);
    }
    
    /**
     * Find existing member by email or mobile for renewal
     */
    public static function findExistingMember($email, $mobile)
    {
        return self::where(function($query) use ($email, $mobile) {
                $query->where('office_email', $email)
                    ->orWhere('mobile', $mobile);
            })
            ->whereNotNull('member_id') // Only return members with assigned ID
            ->orderBy('created_at', 'desc')
            ->first();
    }
    
    /**
     * Get the parent registration (for renewals)
     */
    public function parent()
    {
        return $this->belongsTo(Registration::class, 'parent_member_id', 'member_id');
    }
    
    /**
     * Get all renewals for this member
     */
    public function renewals()
    {
        return $this->hasMany(Registration::class, 'parent_member_id', 'member_id');
    }
    
    /**
     * Check if this is a renewal
     */
    public function isRenewal()
    {
        return !is_null($this->parent_member_id);
    }

    // Accessor for full photo URL
    public function getApplicantPhotoUrlAttribute()
    {
        return $this->applicant_photo_path 
            ? Storage::disk('public')->url($this->applicant_photo_path)
            : null;
    }

    // Accessor for visiting card URL
    public function getVisitingCardUrlAttribute()
    {
        return $this->visiting_card_path 
            ? Storage::disk('public')->url($this->visiting_card_path)
            : null;
    }

    // Accessor for payment screenshot URL
    public function getPaymentScreenshotUrlAttribute()
    {
        return $this->payment_screenshot_path 
            ? Storage::disk('public')->url($this->payment_screenshot_path)
            : null;
    }

    // Get payment mode display name
    public function getPaymentModeDisplayAttribute()
    {
        $modes = [
            'neft' => 'NEFT',
            'upi' => 'UPI',
            'rtgs' => 'RTGS',
            'imps' => 'IMPS',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
        ];

        return $modes[$this->payment_mode] ?? $this->payment_mode;
    }

    // Get registration type display name - UPDATED with all types
    public function getRegistrationTypeDisplayAttribute()
    {
        $types = [
            'epc_classic' => 'EPC Classic',
            'renew_epc_classic' => 'Renew EPC Classic',
            'student' => 'Student',
            'renew_student' => 'Renew Student',
            'dealer_distributor' => 'Dealer/Distributor',
            'renew_dealer_distributor' => 'Renew Dealer/Distributor',
            'silver_corporate' => 'Silver Corporate',
            'renew_silver_corporate' => 'Renew Silver Corporate',
            'gold_corporate' => 'Gold Corporate',
            'renew_gold_corporate' => 'Renew Gold Corporate',
        ];

        return $types[$this->registration_type] ?? $this->registration_type;
    }

    // Get organization type display name
    public function getOrganizationTypeDisplayAttribute()
    {
        $types = [
            'sole_proprietorship' => 'Sole Proprietorship',
            'partnership' => 'Partnership',
            'limited_liability_partnership' => 'Limited Liability Partnership (LLP)',
            'private_limited_company' => 'Private Limited Company',
            'public_limited_company' => 'Public Limited Company',
            'one_person_company' => 'One Person Company (OPC)',
            'other' => 'Other',
        ];

        return $types[$this->organization_type] ?? $this->organization_type;
    }

    // Get business category display name
    public function getBusinessCategoryDisplayAttribute()
    {
        $categories = [
            'student' => 'Student',
            'plumber' => 'Plumber',
            'electrician' => 'Electrician',
            'installer_solar_pv' => 'Installer Solar PV',
            'solar_water_heater' => 'Solar Water Heater',
            'supplier' => 'Supplier',
            'dealer' => 'Dealer',
            'distributor' => 'Distributor',
            'associate_member' => 'Associate Member',
            'manufacturer' => 'Manufacturer',
        ];

        return $categories[$this->business_category] ?? $this->business_category;
    }

    // Check if payment is complete
    public function isPaymentComplete()
    {
        return $this->payment_verified && 
               $this->payment_screenshot_path && 
               $this->transaction_reference;
    }

    // Get payment status with badge class
    public function getPaymentStatusAttribute()
    {
        if ($this->payment_verified) {
            return [
                'label' => 'Verified',
                'class' => 'bg-green-100 text-green-800',
                'icon' => 'check-circle'
            ];
        } elseif ($this->payment_screenshot_path) {
            return [
                'label' => 'Pending Verification',
                'class' => 'bg-yellow-100 text-yellow-800',
                'icon' => 'clock'
            ];
        } else {
            return [
                'label' => 'Payment Pending',
                'class' => 'bg-red-100 text-red-800',
                'icon' => 'x-circle'
            ];
        }
    }

    /**
     * Verify if the provided password matches the hashed password
     */
    public function verifyPassword($password)
    {
        return Hash::check($password, $this->generated_password);
    }

    /**
     * Generate a new secure password (returns plaintext)
     */
    public static function generateNewPassword($length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allCharacters = $uppercase . $lowercase . $numbers . $symbols;
        $password = '';
        
        // Ensure at least one character from each set
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        // Fill the rest with random characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }
        
        // Shuffle the password to randomize character positions
        return str_shuffle($password);
    }

    /**
     * Get the hashed version of a password
     */
    public static function hashPassword($password)
    {
        return Hash::make($password);
    }
}