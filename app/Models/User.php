<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get all the shortlinks belonging to an user
     */
    public function shortlinks()
    {
        return $this->hasMany(Shortlink::class);
    }

    /**
     * Get all the user associated abilities
     */
    public function abilities()
    {
        return $this->belongsToMany(
            Ability::class,
            'user_abilities'
        );
    }

    /**
     * Checks if the user has verified his email
     * by ensuring the email_verified_at is not null
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
}
