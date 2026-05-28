<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuperAdminLoginCode extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Super Admin Login Code')
                    ->html('<p>Your Secure Login Code is: <strong>' . $this->code . '</strong></p><p>This code will expire in 5 minutes.</p>');
    }
}