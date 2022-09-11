<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{

    protected $fillable = [
        'google_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'user_picture',
        'user_email_verified',
        'user_locale',
        'user_verified_email',
        'user_link',
        'user_token',
        'user_refresh_token',
        'expires_in',
        'approved_scopes',
        'avatar_original',
    ];

}
