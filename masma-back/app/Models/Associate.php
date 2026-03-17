<?php
// app/Models/Associate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Associate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'industry',
        'description',
        'logo',
        'order',
        'is_active',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        return Storage::disk('uploads')->url($this->logo);
    }

    public static function getActiveAssociates()
    {
        return self::active()->orderBy('order')->get();
    }
}