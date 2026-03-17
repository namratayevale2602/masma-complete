<?php
// app/Models/VisionMissionGoal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisionMissionGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'icon',
        'items',
        'order',
        'is_active',
    ];

    protected $casts = [
        'items' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function getVision()
    {
        return self::active()->ofType('vision')->first();
    }

    public static function getMission()
    {
        return self::active()->ofType('mission')->first();
    }

    public static function getGoals()
    {
        return self::active()->ofType('goal')->orderBy('order')->get();
    }
}