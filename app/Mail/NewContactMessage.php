<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The sender's name
     */
    public $senderName;

    /**
     * The sender's email
     */
    public $senderEmail;

    /**
     * The sender's phone
     */
    public $senderPhone;

    /**
     * The subject of the message
     */
    public $senderSubject;

    /**
     * The message body
     */
    public $senderMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $senderName,
        $senderEmail,
        $senderPhone,
        $senderSubject,
        $senderMessage
    )
    {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->senderPhone = $senderPhone;
        $this->senderSubject = $senderSubject;
        $this->senderMessage = $senderMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.contact_message')
            ->subject('Nova mensagem recebida - Url Shortner');
    }

}
