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
        'submission_token',
        'ip_address',
        'member_id',
        'parent_member_id',
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
     */
    public static function generateMemberId(): string
    {
        $year = date('Y');
        $prefix = "MASMA-{$year}-";
        
        $lastMember = self::where('member_id', 'like', $prefix . '%')
            ->orderBy('member_id', 'desc')
            ->first();
        
        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->member_id, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
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
            ->whereNotNull('member_id')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
 * Check if member has active verified membership
 */
public static function hasActiveMembership($email, $mobile)
{
    return self::where(function($query) use ($email, $mobile) {
            $query->where('office_email', $email)
                ->orWhere('mobile', $mobile);
        })
        ->where('payment_verified', true)
        ->whereNotNull('member_id')
        ->exists();
}

/**
 * Check if there's a pending renewal for this member
 */
public static function hasPendingRenewal($memberId)
{
    return self::where('parent_member_id', $memberId)
        ->where('payment_verified', false)
        ->where('created_at', '>=', now()->subDays(30))
        ->exists();
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

    /**
     * Get the filename from path
     */
    private function getFilenameFromPath($path)
    {
        if (!$path) return null;
        return basename($path);
    }

    // Accessor for applicant photo URL - Using custom route
    public function getApplicantPhotoUrlAttribute()
    {
        $filename = $this->getFilenameFromPath($this->applicant_photo_path);
        if ($filename) {
            return route('applicant.photo', ['filename' => $filename]);
        }
        return null;
    }

    // Accessor for visiting card URL - Using custom route
    public function getVisitingCardUrlAttribute()
    {
        $filename = $this->getFilenameFromPath($this->visiting_card_path);
        if ($filename) {
            return route('visiting.card', ['filename' => $filename]);
        }
        return null;
    }

    // Accessor for payment screenshot URL - Using custom route
    public function getPaymentScreenshotUrlAttribute()
    {
        $filename = $this->getFilenameFromPath($this->payment_screenshot_path);
        if ($filename) {
            return route('payment.screenshot', ['filename' => $filename]);
        }
        return null;
    }

    /**
     * Get the certificate URL
     */
    public function getCertificateUrlAttribute()
    {
        if (!$this->payment_verified) {
            return null;
        }
        
        $memberId = $this->member_id ?? $this->parent_member_id;
        $certificatePath = storage_path("app/public/certificates/certificate_{$this->id}_{$memberId}.png");
        
        if (file_exists($certificatePath)) {
            return route('certificate.view', $this->id);
        }
        
        return null;
    }

    /**
     * Get the receipt URL
     */
    public function getReceiptUrlAttribute()
    {
        if (!$this->payment_verified) {
            return null;
        }
        
        $memberId = $this->member_id ?? $this->parent_member_id;
        $receiptPath = storage_path("app/public/receipts/receipt_{$this->id}_{$memberId}.png");
        
        if (file_exists($receiptPath)) {
            return route('receipt.view', $this->id);
        }
        
        return null;
    }

    /**
     * Check if certificate exists
     */
    public function hasCertificate()
    {
        if (!$this->payment_verified) {
            return false;
        }
        
        $memberId = $this->member_id ?? $this->parent_member_id;
        $certificatePath = storage_path("app/public/certificates/certificate_{$this->id}_{$memberId}.png");
        
        return file_exists($certificatePath);
    }

    /**
     * Check if receipt exists
     */
    public function hasReceipt()
    {
        if (!$this->payment_verified) {
            return false;
        }
        
        $memberId = $this->member_id ?? $this->parent_member_id;
        $receiptPath = storage_path("app/public/receipts/receipt_{$this->id}_{$memberId}.png");
        
        return file_exists($receiptPath);
    }


     /**
     * Get membership expiry date
     */
    public function getExpiryDateAttribute()
    {
        $originalMember = $this->member_id ? $this : $this->parent;
        if (!$originalMember) {
            $originalMember = $this;
        }
        return $originalMember->created_at->addYear();
    }

    /**
     * Get days until expiry
     */
    public function getDaysLeftAttribute()
    {
        $expiryDate = $this->expiry_date;
        $days = now()->diffInDays($expiryDate, false);
        return $days > 0 ? $days : 0;
    }

    /**
     * Get members expiring in X days
     */
    public static function getMembersExpiringInDays($days)
    {
        $expiryDate = now()->addDays($days)->startOfDay();
        
        return self::where('payment_verified', true)
            ->whereNotNull('member_id')
            ->where(function($query) use ($expiryDate) {
                $query->whereNull('parent_member_id')
                    ->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
            })
            ->orWhere(function($query) use ($expiryDate) {
                $query->whereNotNull('parent_member_id')
                    ->whereDate('created_at', '<=', $expiryDate->copy()->subYear()->endOfDay())
                    ->whereDate('created_at', '>=', $expiryDate->copy()->subYear()->subDays(1)->startOfDay());
            })
            ->get();
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

    // Get registration type display name
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
     * Generate a new secure password
     */
    public static function generateNewPassword($length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allCharacters = $uppercase . $lowercase . $numbers . $symbols;
        $password = '';
        
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        for ($i = 4; $i < $length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }
        
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