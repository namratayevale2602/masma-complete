<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $password;
    public $isPasswordReset;

    public function __construct($registration, $password, $isPasswordReset = false)
    {
        $this->registration = $registration;
        $this->password = $password; // This is PLAINTEXT password
        $this->isPasswordReset = $isPasswordReset;
    }

    public function build()
    {
        $subject = $this->isPasswordReset 
            ? 'Your Password Has Been Reset - ' . config('app.name')
            : 'Your Account Credentials - ' . config('app.name');
            
        return $this->subject($subject)
                    ->view('emails.user-credentials')
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->with([
                        'isPasswordReset' => $this->isPasswordReset,
                    ]);
    }
}