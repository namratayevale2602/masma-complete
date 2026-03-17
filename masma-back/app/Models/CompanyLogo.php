<?php
// app/Models/CompanyLogo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyLogo extends Model
{
    use HasFactory;

    protected $table = 'company_logos';

    protected $fillable = [
        'image',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Scope for active logos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get all active logos ordered by order
    public static function getActiveLogos()
    {
        return self::active()
            ->orderBy('order')
            ->get();
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