<?php

namespace App\Mail;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class VisitorQrCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;
    public $cardUrl;
    public $qrCodeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Visitor $visitor, $cardUrl = null)
    {
        $this->visitor = $visitor;
        $this->cardUrl = $cardUrl ?? config('app.frontend_url', 'http://localhost:5173') . '/visitor/' . $visitor->id . '/card';
        $this->qrCodeUrl = $visitor->qr_code_url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Your Visitor ID Card - ' . config('app.name'))
            ->markdown('emails.visitor-qrcode')
            ->with([
                'visitor' => $this->visitor,
                'appName' => config('app.name'),
                'qrCodeUrl' => $this->qrCodeUrl,
                'cardUrl' => $this->cardUrl,
            ]);

        // Attach QR code image
        if ($this->visitor->qr_code_path && Storage::disk('public')->exists($this->visitor->qr_code_path)) {
            $mail->attach(
                Storage::disk('public')->path($this->visitor->qr_code_path),
                [
                    'as' => 'visitor-qr-code.png',
                    'mime' => 'image/png',
                ]
            );
        }

        return $mail;
    }
}