<?php
// app/Models/HeroImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'desktop_image',
        'mobile_image',
        'order',
        'is_active',
        'alt_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Scope for active images
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // Accessors for full image paths using the uploads disk
    public function getDesktopImageUrlAttribute()
    {
        if (!$this->desktop_image) {
            return null;
        }
        
        // Using Storage::disk('uploads')->url() will generate the correct URL
        return Storage::disk('uploads')->url($this->desktop_image);
    }

    public function getMobileImageUrlAttribute()
    {
        if (!$this->mobile_image) {
            return null;
        }
        
        return Storage::disk('uploads')->url($this->mobile_image);
    }
}