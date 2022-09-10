<?php

namespace App\Mail;

use App\Models\Shortlink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ShortlinkReady extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The shortlink
     *
     * @var string
     */
    public $shortlink;

    /**
     * The long url the shortlink will redirect to
     *
     * @var string
     */
    public $longUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Shortlink  $shortlink
     * @return void
     */
    public function __construct(Shortlink $shortlink)
    {
        $this->shortlink = URL::to('/' . $shortlink->shortstring->shortstring);
        $this->longUrl = $shortlink->redirectUrl->url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->text('emails.shortlink_ready')
            ->subject('Link curto pronto!');
    }
}
