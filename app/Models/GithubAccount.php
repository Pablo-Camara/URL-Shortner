<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GithubAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'github_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'user_token',
        'user_refresh_token',
        'expires_in',
        'approved_scopes',
        'user_url',
        'user_type',
        'user_is_site_admin',
        'user_company',
        'user_blog_link',
        'user_location',
        'user_hireable',
        'user_bio',
        'user_twitter_username',
        'user_total_public_repos',
        'user_total_followers',
        'user_acc_created_at'
    ];
}
