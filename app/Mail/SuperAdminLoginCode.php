<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuperAdminLoginCode extends Mailable
{
    use Queueable, SerializesModels;

    // 🟢 FIXED: Updated variable name to $otp to match what the AuthController provides
    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Super Admin Secure Login Code')
                    ->html('<p>Your Secure Login Code is: <strong>' . $this->otp . '</strong></p><p>This code will expire in 5 minutes.</p>');
    }
}