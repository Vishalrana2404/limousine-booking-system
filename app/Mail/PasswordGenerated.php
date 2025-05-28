<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param  $user
     * @param  $password
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.password_generated')
            ->subject('Your Generated Password');
    }
}
