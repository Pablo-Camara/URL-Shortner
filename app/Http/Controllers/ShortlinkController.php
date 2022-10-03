<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\ShortlinkActions;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use App\Models\Shortlink;
use App\Models\Shortstring;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class ShortlinkController extends Controller
{

    use InteractsWithAuthCookie;

    public function __construct() {
        $this->getUserDataFromCookie();

        View::share('isAdmin', $this->isAdmin());
        View::share('authToken', $this->authToken);
        View::share('isLoggedIn', $this->isLoggedIn() ? 'true' : 'false');
        View::share('userPermissions', json_encode($this->userPermissions));
        View::share('userData', json_encode($this->userData));
    }

     /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function visit($shortstringText, Request $request) {

        $shortstring = Shortstring::where('shortstring', '=', $shortstringText)->with('shortlink')->first();

        if ($shortstring) {
            $shortlink = $shortstring['shortlink'];

            if (!is_null($shortlink)) {

                if ($shortlink->status_id === Shortlink::STATUS_ACTIVE) {
                    UserAction::logAction(
                        $this->userId,
                        ShortlinkActions::VISITED_ACTIVE_SHORTLINK,
                        [
                            'shortlink_id' => $shortlink->id
                        ]
                    );
                    return Redirect::to(
                        $shortlink->redirectUrl->url, 301
                    );
                }

                if ($shortlink->status_id === Shortlink::STATUS_SUSPENDED) {
                    UserAction::logAction(
                        $this->userId,
                        ShortlinkActions::VISITED_SUSPENDED_SHORTLINK,
                        [
                            'shortlink_id' => $shortlink->id
                        ]
                    );
                    return Redirect::to('/');
                }

                if ($shortlink->status_id === Shortlink::STATUS_DELETED) {
                    UserAction::logAction(
                        $this->userId,
                        ShortlinkActions::VISITED_DELETED_SHORTLINK,
                        [
                            'shortlink_id' => $shortlink->id
                        ]
                    );
                    return Redirect::to('/');
                }

            }

            if (
                $shortstring->is_available
            ) {

                UserAction::logAction(
                    $this->userId,
                    ShortlinkActions::VISITED_AVAILABLE_PRESEEDED_SHORTLINK
                );
                return view('home', [
                    'view' => 'RegisterCustomShortlink',
                    'shortstring' => $shortstring->shortstring,
                    'shortlink_available' => true,
                ]);
            }

        }

        UserAction::logAction($this->userId, ShortlinkActions::VISITED_UNSEEDED_SHORTLINK);
        return view('home', [
            'view' => 'RegisterCustomShortlink',
            'shortstring' => $shortstringText
        ]);
    }
}
