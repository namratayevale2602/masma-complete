<?php
// app/Models/Stat.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    use HasFactory;

    protected $table = 'stats';

    protected $fillable = [
        'label',
        'value',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scope for active stats
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get all active stats ordered by order
    public static function getActiveStats()
    {
        return self::active()
            ->orderBy('order')
            ->get();
    }

    // Get default icons mapping
    public static function getDefaultIcons()
    {
        return [
            'FaUserTie' => 'FaUserTie',
            'FaUsers' => 'FaUsers',
            'FaUserCheck' => 'FaUserCheck',
        ];
    }
}