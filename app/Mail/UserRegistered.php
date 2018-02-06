<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /*
        // Testing
        return $this->to('denormalized@gmail.com')
            ->subject('Glazy Registration Successful')
            ->markdown('emails.user.registered');
        */
        return $this->to($this->user->email)
            ->subject('Glazy Registration Successful')
            ->markdown('emails.user.registered');
    }
}
