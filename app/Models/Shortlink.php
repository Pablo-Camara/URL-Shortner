<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    const STATUS_SUSPENDED = 3;

    /**
     * Get the shortstring used for the shortlink
     */
    public function shortstring()
    {
        return $this->belongsTo(Shortstring::class);
    }

    public function redirectUrl() {
        return $this->hasOne(ShortlinkUrl::class)
                    ->where('is_redirect_url', '=', 1);
    }

    public static function moveShortlinksFromUserToUser (
        $fromUserId,
        $toUserId
    ) {
        try {
            $totalRowsUpdated = Shortlink::where(
                'user_id', '=', $fromUserId
            )->update(['user_id' => $toUserId]);
            return $totalRowsUpdated;
        } catch (\Throwable $th) {
            return null;
        }
    }
}
