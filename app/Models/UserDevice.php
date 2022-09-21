<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

class UserDevice extends Model
{

    const UPDATED_AT = null;

    /**
     * Creates a new User Device record, associated to an user
     *
     * @return UserDevice|null
     */
    public static function create(
        $deviceWidth,
        $deviceHeight,
        $userAgentString,
        $userId
    ) {
        try {
            $userDevice = new UserDevice();
            $userDevice->user_id = $userId;

            if (!empty($deviceWidth)) {
                $userDevice->device_width = $deviceWidth;
            }

            if (!empty($deviceHeight)) {
                $userDevice->device_height = $deviceHeight;
            }

            if (!empty($userAgentString)) {

                $userDevice->user_agent = $userAgentString;

                $userAgent = new Agent();
                $userAgent->setUserAgent($userDevice->user_agent);

                $userDevice->is_robot = $userAgent->isRobot();
                $userDevice->is_phone = $userAgent->isPhone();
                $userDevice->is_mobile = $userAgent->isMobile();
                $userDevice->is_tablet = $userAgent->isTablet();
                $userDevice->is_desktop = $userAgent->isDesktop();
                $userDevice->device = $userAgent->device();
                $userDevice->platform = $userAgent->platform();
                $userDevice->browser = $userAgent->browser();
            }

            $userDevice->save();

            return $userDevice;
        } catch (\Throwable $th) {
            return null;
        }
    }


    public static function moveDevicesFromUserToUser (
        $fromUserId,
        $toUserId
    ) {
        try {
            $totalRowsUpdated = UserDevice::where(
                'user_id', '=', $fromUserId
            )->update(['user_id' => $toUserId]);

            return $totalRowsUpdated;
        } catch (\Throwable $th) {
            return null;
        }

    }

}
