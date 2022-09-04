<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $emailConfirmationLink;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user, string $emailConfirmationToken)
    {
        $userFullName = explode(' ', $user->name);
        $this->firstName = ucfirst($userFullName[0]);
        $this->emailConfirmationLink = route('emailConfirmationLink',[
            'token' => urlencode($emailConfirmationToken)
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.auth.email_confirmation')
            ->subject('Confirme o seu email - Encurtador de Urls');
    }
}
