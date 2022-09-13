<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{

    const UPDATED_AT = null;

    public static function logAction($userId, $actionName) {
        try {
            $action = Action::where('name', '=', $actionName)->first();

            if ($action) {
                $userAction = new UserAction();
                $userAction->user_id = $userId;
                $userAction->action_id = $action->id;
                $userAction->ip = request()->ip();
                $userAction->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

}
