<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $adminEmail;

    public function __construct($registration, $adminEmail)
    {
        $this->registration = $registration;
        $this->adminEmail = $adminEmail;
    }

    public function build()
    {
        return $this->subject('New Registration Received - ' . $this->registration->applicant_name)
                    ->view('emails.registration-notification')
                    ->from(config('mail.from.address'), config('mail.from.name'));
    }
}