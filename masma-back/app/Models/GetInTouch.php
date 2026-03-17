<?php
// app/Models/GetInTouch.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GetInTouch extends Model
{
    use HasFactory;

    protected $table = 'get_in_touch';

    protected $fillable = [
        'title',
        'main_title',
        'description',
        'background_image',
        'button_text',
        'button_link',
        'button_icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Get active get in touch content
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get the first active get in touch content
    public static function getActive()
    {
        return self::active()->first();
    }

    // Accessor for background image URL
    public function getBackgroundImageUrlAttribute()
    {
        if (!$this->background_image) {
            return null;
        }
        
        return Storage::disk('uploads')->url($this->background_image);
    }
}