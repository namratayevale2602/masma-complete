<?php
// app/Models/Circular.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Circular extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'category',
        'subcategory',
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

    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        return Storage::disk('uploads')->url($this->file_path);
    }

    public static function getGroupedByCategory()
    {
        return self::active()
            ->orderBy('category')
            ->orderBy('subcategory')
            ->orderBy('order')
            ->get()
            ->groupBy('category');
    }

    public static function getCategories()
    {
        return self::active()
            ->distinct()
            ->pluck('category');
    }
}