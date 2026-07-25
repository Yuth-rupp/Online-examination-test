<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $fullName;
    public $newPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($fullName, $newPassword)
    {
        $this->fullName    = $fullName;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your ExamSystem Password Has Been Reset')
            ->html(
                '<p>Hi ' . e($this->fullName) . ',</p>' .
                '<p>A Super Admin has reset your password. Your new temporary password is:</p>' .
                '<p style="font-size:18px;font-weight:700;letter-spacing:1px;">' . e($this->newPassword) . '</p>' .
                '<p>Please log in with this password and change it as soon as possible.</p>' .
                '<p>If you did not request this, contact your Super Admin immediately.</p>'
            );
    }
}