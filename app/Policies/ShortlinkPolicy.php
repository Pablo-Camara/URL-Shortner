<?php

namespace App\Policies;

use App\Models\Shortlink;
use App\Models\User;

class ShortlinkPolicy
{
    public function view(User $user, Shortlink $link): bool
    {
        return $link->user_id === $user->id;
    }

    public function update(User $user, Shortlink $link): bool
    {
        return $this->view($user, $link);
    }
}
