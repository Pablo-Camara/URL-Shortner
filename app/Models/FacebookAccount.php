<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $fillable = [
        'facebook_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'user_token',
        'user_refresh_token',
        'expires_in',
        'approved_scopes',
        'avatar_original',
        'profile_url'
    ];
}
