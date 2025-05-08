<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomEmailVerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function build(){
        $this->to($this->user->email)->view('email.custom-email-verfication')
        ->subject('Verify your email address')
        ->with([
            'user' => $this->user,
            'verificationUrl' => $this->verificationUrl,
        ]);
    }
}
