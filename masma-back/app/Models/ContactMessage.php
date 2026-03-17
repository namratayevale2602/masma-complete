<?php
// app/Models/ContactMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'consent_given',
        'is_read',
        'is_replied',
        'reply_message',
        'replied_at',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'is_read' => 'boolean',
        'is_replied' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }

    public function markAsReplied($replyMessage = null)
    {
        $this->is_replied = true;
        $this->replied_at = now();
        if ($replyMessage) {
            $this->reply_message = $replyMessage;
        }
        $this->save();
    }
}