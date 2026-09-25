<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortstring extends Model
{
    protected $guarded = ['id'];

    public function shortlink()
    {
        return $this->hasOne(Shortlink::class);
    }
}
