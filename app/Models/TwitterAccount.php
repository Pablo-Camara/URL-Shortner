<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwitterAccount extends Model
{

    protected $fillable = [
        'twitter_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'avatar_original',
        'user_protected',
        'user_followers_count',
        'user_friends_count',
        'user_listed_count',
        'user_created_at',
        'user_favourites_count',
        'user_utc_offset',
        'user_time_zone',
        'user_geo_enabled',
        'user_verified',
        'user_statuses_count',
        'user_lang',
        'user_contributors_enabled',
        'user_is_translator',
        'user_is_translation_enabled',
        'user_profile_use_background_image',
        'user_has_extended_profile',
        'user_default_profile',
        'user_default_profile_image',
        'user_suspended',
        'user_needs_phone_verification',
        'user_url',
        'user_location',
        'user_description',
        'user_token',
        'user_token_secret',
    ];

}
