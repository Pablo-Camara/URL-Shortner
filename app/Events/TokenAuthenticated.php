<?php

namespace App\Models\Sanctum\Events;

class TokenAuthenticated
{
    /**
     * The personal access token that was authenticated.
     *
     * @var \App\Models\Sanctum\PersonalAccessToken
     */
    public $token;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Sanctum\PersonalAccessToken  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
}
