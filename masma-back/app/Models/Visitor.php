<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_name',
        'bussiness_name',
        'mobile',
        'phone',
        'whatsapp_no',
        'email',
        'city',
        'town',
        'village',
        'remark',
        'qr_code_path',
        'qr_code_data',
    ];

    protected $appends = ['qr_code_url', 'qr_code_download_url'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the QR code URL for web display
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path && Storage::disk('public')->exists($this->qr_code_path)) {
            return Storage::disk('public')->url($this->qr_code_path);
        }
        return null;
    }

    /**
 * Get QR code as base64 string
 */
public function getQrCodeBase64Attribute()
{
    if (!$this->qr_code_path || !Storage::disk('public')->exists($this->qr_code_path)) {
        return null;
    }
    
    $imageData = Storage::disk('public')->get($this->qr_code_path);
    return base64_encode($imageData);
}

    /**
     * Get the QR code download URL
     */
    public function getQrCodeDownloadUrlAttribute()
    {
        if ($this->qr_code_path) {
            return route('visitor.qrcode.download', $this->id);
        }
        return null;
    }
}