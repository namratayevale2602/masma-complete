<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $certificatePath;
    public $receiptPath;
    public $password;
    public $memberId;

    public function __construct(Registration $registration, $certificatePath, $receiptPath = null, $password = null)
    {
        $this->registration = $registration;
        $this->certificatePath = $certificatePath;
        $this->receiptPath = $receiptPath;
        $this->password = $password;
        $this->memberId = $registration->member_id ?? $registration->parent_member_id;
    }

    public function build()
    {
        $subject = $this->registration->isRenewal() 
            ? 'Membership Renewal Confirmation - MASMA' 
            : 'Welcome to MASMA - Membership Confirmation';
        
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->view('emails.membership-confirmation')
            ->with([
                'registration' => $this->registration,
                'memberId' => $this->memberId,
                'membershipPlan' => $this->registration->getRegistrationTypeDisplayAttribute(),
                'isRenewal' => $this->registration->isRenewal(),
                'password' => $this->password,
                'hasPassword' => !is_null($this->password),
            ]);

        // Attach certificate (PNG)
        if (file_exists($this->certificatePath)) {
            $email->attach($this->certificatePath, [
                'as' => 'membership-certificate.png',
                'mime' => 'image/png',
            ]);
        }

        // Attach payment receipt (PNG)
        if ($this->receiptPath && file_exists($this->receiptPath)) {
            $email->attach($this->receiptPath, [
                'as' => 'payment-receipt.png',
                'mime' => 'image/png',
            ]);
        }

        return $email;
    }
}