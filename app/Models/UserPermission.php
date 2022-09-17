<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{

    public function toArray()
    {
        return [
            'edit_shortlinks_destination_url' => (bool)$this->edit_shortlinks_destination_url
        ];
    }
}
