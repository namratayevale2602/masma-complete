<?php
// app/Models/BoardDirector.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BoardDirector extends Model
{
    use HasFactory;

    protected $table = 'board_directors';

    protected $fillable = [
        'name',
        'place',
        'designation',
        'education',
        'experience',
        'image',
        'order',
        'is_active',
        'year',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Scope for active directors
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for current year
    public function scopeCurrentYear($query, $year = '2025-26')
    {
        return $query->where('year', $year);
    }

    // Get all active directors ordered by order
    public static function getActiveDirectors($year = '2025-26')
    {
        return self::active()
            ->currentYear($year)
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