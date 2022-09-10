<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\ShortlinkActions;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use App\Mail\ShortlinkReady;
use App\Models\Shortlink;
use App\Models\ShortlinkUrl;
use App\Models\Shortstring;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ShortlinkController extends Controller
{

    use InteractsWithAuthCookie;

    public function __construct() {
        $this->getUserDataFromCookie();
    }

     /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function visit($shortstring, Request $request) {

        $shortstring = Shortstring::where('shortstring', '=', $shortstring)->with('shortlink')->first();

        if ($shortstring) {
            $shortlink = $shortstring['shortlink'];

            if (!is_null($shortlink)) {

                if (!is_null($this->userId)) {
                    UserAction::logAction($this->userId, ShortlinkActions::VISITED_ACTIVE_SHORTLINK);
                }

                return Redirect::to(
                    $shortlink->redirectUrl->url, 301
                );
            }

            if ($shortstring->is_available) {
                $captchaSitekey = config('captcha.sitekey');

                if (!is_null($this->userId)) {
                    UserAction::logAction($this->userId, ShortlinkActions::VISITED_AVAILABLE_SHORTLINK);
                }

                return view('home', [
                    'shortlink' => url('/' . $shortstring->shortstring),
                    'shortlink_available' => true,
                    'shortlink_shortstring' => $shortstring->shortstring,
                    'captchaSitekey' => $captchaSitekey
                ]);
            }

        }

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, ShortlinkActions::VISITED_UNEXISTING_AND_UNAVAILABLE_SHORTLINK);
        }
        // shortstring not available
        return redirect('/');
    }

    /**
     * Returns the users links ( be him a guest or not )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myLinks(Request $request)
    {
        //TODO: pagination
        $userShortlinks = $request->user()->shortlinks()
        ->select(['id', 'destination_email', 'shortstring_id', 'created_at'])
        ->with(
            [
                'shortstring' => function ($query) {
                    $query->select(['id','shortstring']);
                },
            ],
         )
         ->with(
            [
                'redirectUrl' => function ($query) {
                    $query->select(['id', 'shortlink_id','url']);
                }
            ]
         )
         ->orderBy('id', 'desc')
         ->get()->toArray();

        $userShortlinks = array_map(
            function ($row) {
                return [
                    'id' => $row['id'],
                    'long_url' => $row['redirect_url']['url'],
                    'shortlink' => URL::to('/' . $row['shortstring']['shortstring']),
                    'destination_email' => $row['destination_email'],
                    'created_at' => $row['created_at']
                ];
            }, $userShortlinks
        );

        if (!is_null($this->userId)) {
            UserAction::logAction($this->userId, ShortlinkActions::VIEWED_LINKS_LIST);
        }
        return new Response($userShortlinks);
    }

    /**
     * Register/point available shortstring to a long url
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerAvailable(Request $request)
    {
        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers
        $request->validate([
            'long_url' => 'required|url|max:2048', //TODO: also log when max validation fails
            'g-recaptcha-response' => 'required|captcha'
        ]);

        $shortstring = $request->input('shortstring');

        $shortstring = Shortstring::where('shortstring', '=', $shortstring)->first();

        if (!$shortstring) {
            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, ShortlinkActions::ATTEMPTED_TO_REGISTER_UNEXISTING_SHORTSTRING);
            }
            return new Response([
                //TODO: translate
                'message' => 'Este link não está disponível!'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$shortstring->is_available) {

            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, ShortlinkActions::ATTEMPTED_TO_REGISTER_UNAVAILABLE_SHORTSTRING);
            }

            return new Response([
                //TODO: translate
                'message' => 'Este link já não está disponível!'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }


        $newShortlink = new Shortlink();
        $newShortlink->user_id = $request->user()->id;
        $newShortlink->shortstring_id = $shortstring->id;
        $newShortlink->status_id = Shortlink::STATUS_ACTIVE;

        DB::beginTransaction();
        try {
            $newShortlink->save();

            $shortlinkUrl = new ShortlinkUrl();
            $shortlinkUrl->url = $request->input('long_url');
            $shortlinkUrl->shortlink_id = $newShortlink->id;
            $shortlinkUrl->is_redirect_url = true;
            $shortlinkUrl->save();

            $shortstring->is_available = 0;
            $shortstring->save();

            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, ShortlinkActions::REGISTERED_CUSTOM_AVAILABLE_SHORTSTRING);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        return new Response(
            [
                'shortlink' => URL::to('/' . $shortstring->shortstring)
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function shorten(Request $request)
    {
        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers

        $maxUrlLength = 2048;

        try {

            Validator::make(
                $request->all(),
                [
                    // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                    'long_url' => 'required|url|max:' . $maxUrlLength,
                    'destination_email' => 'email:rfc,dns',
                    'g-recaptcha-response' => 'required|captcha'
                ],
                [],
                ['long_url' => 'URL']
            )->validate();

        } catch (ValidationException $ve) {
            if (
                $request->input('long_url', '')
                &&
                strlen($request->input('long_url')) > $maxUrlLength
                &&
                !is_null($this->userId)
            ) {
                UserAction::logAction(
                    $this->userId,
                    ShortlinkActions::ATTEMPTED_TO_GENERATE_SHORTLINK_FOR_URL_THAT_IS_TOO_LONG
                );
            }
            throw $ve;
        } catch (\Throwable $th) {
            throw $th;
        }


        $usePreseededShortstrings = config('app.use_preseeded_shortstrings');
        $useBcIfPreseededShortstringsEnd = config('app.use_bc_if_preseeded_shortstrings_end');

        $useBcToGenerateShortstring = false;

        if ($usePreseededShortstrings) {
            $nextAvailableShortstring = Shortstring::where('is_available', 1)->first();

            if (
                !$nextAvailableShortstring
                &&
                $useBcIfPreseededShortstringsEnd
            ) {
                if (!is_null($this->userId)) {
                    UserAction::logAction($this->userId, ShortlinkActions::DID_NOT_FIND_AVAILABLE_PRESEEDED_SHORTSTRING);
                }
                $useBcToGenerateShortstring = true;
            }
        } else {
            $useBcToGenerateShortstring = true;
        }

        if ($useBcToGenerateShortstring) {
            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, ShortlinkActions::WILL_TRY_GENERATING_SHORTSTRING_WITH_BC);
            }

            $nextAvailableShortstring = $this->tryGeneratingShortstringWithBaseConvert();
        }

        if (!$nextAvailableShortstring) {
            // this should never happen
            // but in case we ever run out of shortstrings/available links
            // we should be notified.

            //TODO: Send email notification.
            if (!is_null($this->userId)) {
                UserAction::logAction($this->userId, ShortlinkActions::FOUND_NO_AVAILABLE_SHORTSTRING);
            }

            return new Response(
                [
                    'message' => 'Estamos sem links disponíveis! Volte a tentar dentro de alguns minutos!'
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $newShortlink = new Shortlink();
        $newShortlink->user_id = $request->user()->id;
        $newShortlink->shortstring_id = $nextAvailableShortstring->id;

        if (
            !is_null($request->input('destination_email'))
            &&
            !is_null($this->userId) && $this->guest === 0
        ) {
            $newShortlink->destination_email = $request->input('destination_email');
        }

        $newShortlink->status_id = Shortlink::STATUS_ACTIVE;

        DB::beginTransaction();
        try {
            $newShortlink->save();

            $shortlinkUrl = new ShortlinkUrl();
            $shortlinkUrl->url = $request->input('long_url');
            $shortlinkUrl->shortlink_id = $newShortlink->id;
            $shortlinkUrl->is_redirect_url = true;
            $shortlinkUrl->save();

            $nextAvailableShortstring->is_available = 0;
            $nextAvailableShortstring->save();

            if ($useBcToGenerateShortstring) {
                UserAction::logAction($newShortlink->user_id, ShortlinkActions::GENERATED_SHORTLINK_WITH_BC);
            } else {
                UserAction::logAction($newShortlink->user_id, ShortlinkActions::GENERATED_SHORTLINK_WITH_PRESEEDED_STRING);
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        if (
            !is_null($newShortlink->destination_email)
            &&
            !is_null($this->userId) && $this->guest === 0
        ) {
            Mail::to(
                $newShortlink->destination_email
            )->queue(new ShortlinkReady($newShortlink));

            UserAction::logAction($this->userId, ShortlinkActions::SENT_SHORTLINK_TO_EMAIL);
        }

        return new Response(
            [
                'shortlink' => URL::to('/' . $nextAvailableShortstring->shortstring)
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Generates a shortstring with base_convert
     * based on a number (autoincrement value to avoid dupes)
     *
     * @param int $currentAutoincrementValue
     * @return void
     */
    private function generateShortstringWithBaseConvert(int $currentAutoincrementValue) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $totalAlphabetChars = strlen($alphabet);
        $minimumShortstringLength = config('app.minimum_shortstrings_length');

        if (
            !is_numeric($minimumShortstringLength)
            ||
            $minimumShortstringLength < 1
        ) {
            // precautions..
           $minimumShortstringLength = 1;
        }

        $shortstring = base_convert(
            ($currentAutoincrementValue - 1 /* minus 1, to not skip first convertion */) + ($totalAlphabetChars**($minimumShortstringLength-1)),
            10,
            $totalAlphabetChars
        );

        return $shortstring;
    }


    /**
     * Attempts to generate a shortstring and return it
     * if not successful will return null
     *
     * @return Shortstring|null
     */
    private function tryGeneratingShortstringWithBaseConvert() {
        $shortstringCreated = false;
        $totalAttempts = 0;
        $nextAvailableShortstring = null;
        while ($shortstringCreated == false) {
            try {
                $shortstringsTableStatus = DB::select(
                    "SHOW TABLE STATUS LIKE 'shortstrings'"
                );
                $currAutoincrementVal = $shortstringsTableStatus[0]->Auto_increment;
                $shortstringToAdd = $this->generateShortstringWithBaseConvert($currAutoincrementVal);

                $nextAvailableShortstring = new Shortstring();
                $nextAvailableShortstring->shortstring = $shortstringToAdd;

                // we make it available
                // until down below we use it
                // and make it unavailable
                $nextAvailableShortstring->is_available = 1;
                $nextAvailableShortstring->save();
                $shortstringCreated = true;
            } catch (\Throwable $th) {
                $totalAttempts++;
            }

            // TODO: set max attempts in env variable / app config
            // avoid infinite loop
            if ($totalAttempts >= 20) {
                //TODO: log event ( to monitor if this ever happens )
                // should never happen but just in case..
                if (!is_null($this->userId)) {
                    UserAction::logAction($this->userId, ShortlinkActions::REACHED_MAX_GENERATE_ATTEMPTS_WITH_BC);
                }
                break;
            }
        }

        return $nextAvailableShortstring;
    }
}
