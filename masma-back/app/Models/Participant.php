<?php
// app/Models/Participant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Participant extends Model
{
    use HasFactory;

    protected $table = 'participants';

    protected $fillable = [
        'image',
        'row',
        'order',
        'is_active',
        'alt_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'row' => 'integer',
        'order' => 'integer',
    ];

    // Scope for active participants
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific row
    public function scopeRow($query, $rowNumber)
    {
        return $query->where('row', $rowNumber);
    }

    // Accessor for full image path using uploads disk
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        return Storage::disk('uploads')->url($this->image);
    }

    // Get participants by row with proper ordering
    public static function getParticipantsByRow()
    {
        $participants = self::active()->orderBy('order')->get();
        
        return [
            'row1' => $participants->where('row', 1)->values(),
            'row2' => $participants->where('row', 2)->values(),
        ];
    }
}