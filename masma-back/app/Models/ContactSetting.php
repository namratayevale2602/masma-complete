<?php
// app/Models/ContactSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $table = 'contact_settings';

    protected $fillable = [
        'page_title',
        'page_description',
        'contact_info',
        'office_address',
        'phone',
        'email',
        'working_hours_weekdays',
        'working_hours_saturday',
        'map_embed_url',
        'form_title',
        'form_description',
        'is_active',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getActiveSettings()
    {
        return self::active()->first();
    }

    public function getFormattedContactInfo()
    {
        if ($this->contact_info) {
            return $this->contact_info;
        }

        // Default contact info based on your component
        return [
            [
                'icon' => 'FaMapMarkerAlt',
                'title' => 'Our Office',
                'details' => [$this->office_address ?? 'THE MAHARASHTRA SOLAR MANUFACTURES ASSOCIATION D-93, 4th Floor,Office No.93, G-Wing, S.No. 19A/3B,Pune - Satara Rd, KK Market, Ahilya devi chowk Dhankawadi, Pune, Maharashtra 411043'],
            ],
            [
                'icon' => 'FaPhone',
                'title' => 'Phone Number',
                'details' => [$this->phone ?? '+91 93091 67947'],
            ],
            [
                'icon' => 'FaEnvelope',
                'title' => 'Email Address',
                'details' => [$this->email ?? 'info@masma.in'],
            ],
            [
                'icon' => 'FaClock',
                'title' => 'Working Hours',
                'details' => [
                    $this->working_hours_weekdays ?? 'Monday - Friday: 9:00 AM - 6:00 PM',
                    $this->working_hours_saturday ?? 'Saturday: 9:00 AM - 2:00 PM',
                ],
            ],
        ];
    }
}