<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    public const ACTIVE = 1;

    public const ARCHIVED = 2;

    public const PAUSED = 3;

    protected $guarded = ['id'];

    protected $casts = ['expires_at' => 'datetime', 'version' => 'integer'];

    public function shortstring()
    {
        return $this->belongsTo(Shortstring::class);
    }

    public function destination()
    {
        return $this->hasOne(ShortlinkUrl::class)->where('is_redirect_url', true);
    }

    public function history()
    {
        return $this->hasMany(ShortlinkUrl::class)->orderByDesc('id');
    }

    public function clicks()
    {
        return $this->hasMany(DailyClick::class);
    }

    public function status(): string
    {
        if ($this->status_id === self::ARCHIVED) {
            return 'archived';
        }
        if ($this->status_id === self::PAUSED) {
            return 'paused';
        }

        return $this->expires_at?->isPast() ? 'expired' : 'active';
    }
}
