<?php
// app/Models/MembershipPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'membership_fee',
        'registration_charges',
        'duration',
        'features',
        'order',
        'is_highlighted',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'order' => 'integer',
        'is_highlighted' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getActivePlans()
    {
        return self::active()->orderBy('order')->get();
    }
}