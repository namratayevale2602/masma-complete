<?php
// app/Models/Gallery.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'featured_image',
        'images',
        'order',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return null;
        }
        return Storage::disk('uploads')->url($this->featured_image);
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images) {
            return [];
        }
        return array_map(function ($image) {
            return Storage::disk('uploads')->url($image);
        }, $this->images);
    }

    public static function getActiveGalleries()
    {
        return self::active()->orderBy('order')->get();
    }
}