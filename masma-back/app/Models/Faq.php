<?php
// app/Models/Faq.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
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

    public static function getCategories()
    {
        return self::active()->distinct()->pluck('category');
    }

    public static function getByCategory($category = null)
    {
        $query = self::active()->orderBy('order');
        
        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }
        
        return $query->get();
    }
}