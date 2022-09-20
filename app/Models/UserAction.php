<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{

    const UPDATED_AT = null;

    public static function logAction($userId, $actionName, $extraParams = []) {
        try {
            $action = Action::where('name', '=', $actionName)->first();

            if ($action) {
                $userAction = new UserAction();
                $userAction->user_id = $userId;
                $userAction->action_id = $action->id;
                $userAction->ip = request()->ip();

                foreach($extraParams as $paramName => $paramValue) {
                    $userAction->$paramName = $paramValue;
                }
                $userAction->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public static function moveActionsFromUserToUser (
        $fromUserId,
        $toUserId
    ) {
        try {
            $totalRowsUpdated = UserAction::where(
                'user_id', '=', $fromUserId
            )->update(['user_id' => $toUserId]);
            return $totalRowsUpdated;
        } catch (\Throwable $th) {
            return null;
        }
    }

}
