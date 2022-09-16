<?php

namespace App\Models;

use App\Helpers\Auth\Abilities\AdminAbilities;
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
     * Gets the permissions configured for this user
     */
    public function userPermissions() {
        return $this->hasOne(UserPermission::class);
    }

    /**
     * Checks if the user has verified his email
     * by ensuring the email_verified_at is not null
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function isGuest() {
        return $this->guest === 1;
    }

    public function isAdmin () {
        $isAdmin = false;

        try {
            $adminAbility = $this->abilities()->where('name', '=', AdminAbilities::ADMIN)->first();

            if ($adminAbility) {
                $isAdmin = true;
            }

        } catch (\Throwable $th) { }

        return $isAdmin;
    }

    /**
     * Creates a new guest user or returns null
     *
     * @return User|null
     */
    public static function createNewGuestUser() {
        try {
            $user = new User();
            $user->guest = 1;
            $user->save();
            return $user;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function updateAvatar($userId, $newAvatar) {
        User::where(
            'id', '=', $userId
        )->update(['avatar' => $newAvatar]);
    }

}
