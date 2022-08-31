<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;
    const STATUS_SUSPENDED = 3;
}
