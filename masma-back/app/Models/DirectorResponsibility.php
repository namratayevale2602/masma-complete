<?php
// app/Models/DirectorResponsibility.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectorResponsibility extends Model
{
    use HasFactory;

    protected $fillable = [
        'task',
        'icon',
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

    public static function getActiveResponsibilities()
    {
        return self::active()->orderBy('order')->get();
    }
}