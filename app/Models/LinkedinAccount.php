<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinAccount extends Model
{

    protected $fillable = [
        'linkedin_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'user_token',
        'user_refresh_token',
        'expires_in',
        'approved_scopes',
        'avatar_original'
    ];

}
