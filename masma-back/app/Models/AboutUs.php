<?php
// app/Models/AboutUs.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'about_us';

    protected $fillable = [
        'title',
        'description',
        'image',
        'badge_number',
        'badge_label',
        'badge_subtext',
        'button_text',
        'button_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Get active about us content
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get the first active about us content
    public static function getActive()
    {
        return self::active()->first();
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        return Storage::disk('uploads')->url($this->image);
    }
}