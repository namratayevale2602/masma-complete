<?php
// app/Models/AboutMasma.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutMasma extends Model
{
    use HasFactory;

    protected $table = 'about_masma';

    protected $fillable = [
        'title',
        'president_name',
        'president_title',
        'president_image',
        'president_message',
        'president_message_2',
        'president_message_3',
        'stats_1_label',
        'stats_1_value',
        'stats_2_label',
        'stats_2_value',
        'stats_3_label',
        'stats_3_value',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Scope for active about masma content
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get the first active about masma content
    public static function getActive()
    {
        return self::active()->first();
    }

    // Accessor for president image URL
    public function getPresidentImageUrlAttribute()
    {
        if (!$this->president_image) {
            return null;
        }
        
        return Storage::disk('uploads')->url($this->president_image);
    }

    // Get stats as array
    public function getStatsAttribute()
    {
        return [
            [
                'label' => $this->stats_1_label ?? 'Years of Experience',
                'value' => $this->stats_1_value ?? '20+',
            ],
            [
                'label' => $this->stats_2_label ?? 'Member Companies',
                'value' => $this->stats_2_value ?? '500+',
            ],
            [
                'label' => $this->stats_3_label ?? 'Projects Completed',
                'value' => $this->stats_3_value ?? '1000+',
            ],
        ];
    }
}