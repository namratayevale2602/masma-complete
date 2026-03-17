<?php
// app/Models/CtaCard.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtaCard extends Model
{
    use HasFactory;

    protected $table = 'cta_cards';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'color',
        'stats',
        'link',
        'button_text',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scope for active cards
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get all active cta cards ordered by order
    public static function getActiveCards()
    {
        return self::active()
            ->orderBy('order')
            ->get();
    }
}