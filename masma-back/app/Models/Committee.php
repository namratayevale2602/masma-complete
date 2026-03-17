<?php
// app/Models/Committee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_title',
        'category_icon',
        'member_name',
        'member_city',
        'member_position',
        'member_image',
        'category_order',
        'member_order',
        'is_active',
    ];

    protected $casts = [
        'category_order' => 'integer',
        'member_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getMemberImageUrlAttribute()
    {
        if (!$this->member_image) {
            return null;
        }
        return Storage::disk('uploads')->url($this->member_image);
    }

    public static function getGroupedByCategory()
    {
        return self::active()
            ->orderBy('category_order')
            ->orderBy('member_order')
            ->get()
            ->groupBy('category_title');
    }
}