<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class RegisteredUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'registrations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'office_email',
        'generated_password',
        'applicant_name',
        'organization',
        'mobile',
        'payment_verified',
        'credentials_sent',
    ];

    protected $hidden = [
        'generated_password',
        'remember_token',
    ];

    protected $casts = [
        'payment_verified' => 'boolean',
        'credentials_sent' => 'boolean',
        'credentials_sent_at' => 'datetime',
    ];

    public function getAuthIdentifierName()
    {
        return 'office_email';
    }

    public function getAuthPassword()
    {
        return $this->generated_password;
    }

    public function isActive()
    {
        return $this->payment_verified && $this->credentials_sent;
    }

    public function tokens()
    {
        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
    }
}