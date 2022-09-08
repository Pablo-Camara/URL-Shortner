<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecovery extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $changePasswordLink;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user, string $pwdRecoveryToken)
    {
        $userFullName = explode(' ', $user->name);
        $this->firstName = ucfirst($userFullName[0]);
        $this->changePasswordLink = route('changePasswordLink',[
            'token' => urlencode($pwdRecoveryToken)
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.auth.password_recovery')
            ->subject('Recuperar password - Encurtador de Urls');
    }
}
