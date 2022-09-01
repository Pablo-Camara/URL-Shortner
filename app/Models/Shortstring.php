<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortstring extends Model
{

    /**
     * Get the shortlinks that used this shortstring, in case any
     */
    public function shortlink()
    {
        return $this->hasOne(Shortlink::class);
    }

}
