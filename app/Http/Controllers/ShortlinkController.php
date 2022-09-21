<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\ShortlinkActions;
use App\Helpers\Auth\Traits\InteractsWithAuthCookie;
use App\Mail\ShortlinkReady;
use App\Models\Action;
use App\Models\Shortlink;
use App\Models\ShortlinkUrl;
use App\Models\Shortstring;
use App\Models\UserAction;
use App\Models\UserPermission;
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

            if ($shortstring->is_available) {

                UserAction::logAction(
                    $this->userId,
                    ShortlinkActions::VISITED_AVAILABLE_SHORTLINK
                );
                return view('home', [
                    'view' => 'RegisterAvailableShortlink',
                    'shortlink' => url('/' . $shortstring->shortstring),
                    'shortlink_available' => true,
                    'shortlink_shortstring' => $shortstring->shortstring
                ]);
            }

        }


        UserAction::logAction($this->userId, ShortlinkActions::VISITED_UNEXISTING_AND_UNAVAILABLE_SHORTLINK);
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
        $selectFields = [
            'id',
            'destination_email',
            'shortstring_id',
            'created_at'
        ];

        if ($this->isLoggedIn()) {
            $activeVisitsActionId = Action::where('name', '=', ShortlinkActions::VISITED_ACTIVE_SHORTLINK)->first();

            if (
                $activeVisitsActionId
            ) {
                /**
                 * @var UserPermission
                 */
                $userPermissions = UserPermission::where('user_id', $request->user()->id)->first();

                if (
                    $userPermissions->canViewShortlinksTotalViews()
                ) {
                    array_push(
                        $selectFields,
                        DB::raw(
                            '(
                                SELECT COUNT(*) FROM user_actions
                                WHERE
                                    user_actions.shortlink_id = shortlinks.id
                                    AND
                                    user_actions.action_id = '.$activeVisitsActionId->id.'
                            ) AS total_views')
                    );
                }

                if (
                    $userPermissions->canViewShortlinksTotalUniqueViews()
                ) {
                    array_push(
                        $selectFields,
                        DB::raw(
                            '(
                                SELECT COUNT(DISTINCT ip) FROM user_actions
                                WHERE
                                    user_actions.shortlink_id = shortlinks.id
                                    AND
                                    user_actions.action_id = '.$activeVisitsActionId->id.'
                            ) AS total_unique_views')
                    );
                }

            }

        }

        $userShortlinks = $request->user()->shortlinks()
        ->select(
            $selectFields
        )
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
         ->where('status_id', '=', Shortlink::STATUS_ACTIVE)
         ->orderBy('id', 'desc')
         ->paginate(3)
         ->through(function($shortlink){
            $shortlink->long_url = $shortlink->redirectUrl->url;
            $shortlink->shortlink = URL::to('/' . $shortlink->shortstring->shortstring);

            unset($shortlink->shortstring);
            unset($shortlink->shortstring_id);
            unset($shortlink->redirectUrl);
            return $shortlink;
        });


        UserAction::logAction($this->userId, ShortlinkActions::VIEWED_LINKS_LIST);

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

        $maxUrlLength = 2048;

        try {
            $validations = [
                // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                'long_url' => 'required|url|max:' . $maxUrlLength,
            ];

            $enableCaptchaSitekey = config('captcha.enable');
            if ($enableCaptchaSitekey) {
                $validations['g-recaptcha-response'] = 'required|captcha';
            }
            Validator::make(
                $request->all(),
                $validations,
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

        if ($request->getHost() === parse_url($request->input('long_url'))['host']) {
            UserAction::logAction(
                $this->userId,
                ShortlinkActions::ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK
            );

            throw ValidationException::withMessages(['long_url' => 'Não é possível criar um link curto para este url.']);
        }

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
     * Generates a new shortlink
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

            $validations = [
                // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                'long_url' => 'required|url|max:' . $maxUrlLength,
                'destination_email' => 'email:rfc,dns',
            ];

            $enableCaptchaSitekey = config('captcha.enable');
            if ($enableCaptchaSitekey) {
                $validations['g-recaptcha-response'] = 'required|captcha';
            }

            Validator::make(
                $request->all(),
                $validations,
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

        if ($request->getHost() === parse_url($request->input('long_url'))['host']) {
            UserAction::logAction(
                $this->userId,
                ShortlinkActions::ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK
            );

            throw ValidationException::withMessages(['long_url' => 'Não é possível criar um link curto para este url.']);
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
                UserAction::logAction(
                    $newShortlink->user_id,
                    ShortlinkActions::GENERATED_SHORTLINK_WITH_BC,
                    [
                        'shortlink_id' => $newShortlink->id
                    ]
                );
            } else {
                UserAction::logAction(
                    $newShortlink->user_id,
                    ShortlinkActions::GENERATED_SHORTLINK_WITH_PRESEEDED_STRING,
                    [
                        'shortlink_id' => $newShortlink->id
                    ]
                );
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
     * edits destination url of a shortlink keeping history
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editShortlinkUrl(Request $request)
    {
        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers

        $maxUrlLength = 2048; //TODO: use global config/env variable

        try {

            $validations = [
                // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                'long_url' => 'required|url|max:' . $maxUrlLength,
                'shortlink_id' => 'required|numeric',
            ];

            $enableCaptchaSitekey = config('captcha.enable');
            if ($enableCaptchaSitekey) {
                $validations['g-recaptcha-response'] = 'required|captcha';
            }

            Validator::make(
                $request->all(),
                $validations,
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
                    ShortlinkActions::ATTEMPTED_TO_EDIT_SHORTLINK_URL_TO_ONE_THAT_IS_TOO_LONG
                );
            }
            throw $ve;
        } catch (\Throwable $th) {
            throw $th;
        }

        if ($request->getHost() === parse_url($request->input('long_url'))['host']) {
            UserAction::logAction(
                $this->userId,
                ShortlinkActions::ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK
            );

            throw ValidationException::withMessages(['long_url' => 'Não é possível criar um link curto para este url.']);
        }


        $shortlink = Shortlink::findOrFail($request->input('shortlink_id'));


        // check user owns shortlink / has permission to edit the url
        if ($shortlink->user_id !== $request->user()->id) {
            //TODO: log that someone tried to edit someone elses shortlink redirect url
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        /**
         * @var UserPermission
         */
        $userPermissions = UserPermission::where('user_id', $request->user()->id)->first();

        if ($userPermissions->canEditShortlinksDestinationUrl() == false) {
            //TODO: user has no permission to edit shortlinks destination urls
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {

            ShortlinkUrl::where('shortlink_id', '=', $shortlink->id)->update(
                ['is_redirect_url' => false]
            );
            $shortlinkUrl = new ShortlinkUrl();
            $shortlinkUrl->url = $request->input('long_url');
            $shortlinkUrl->shortlink_id = $shortlink->id;
            $shortlinkUrl->is_redirect_url = true;
            $shortlinkUrl->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        UserAction::logAction(
            $this->userId,
            ShortlinkActions::EDITED_SHORTLINK_DESTINATION_URL,
            [
                'shortlink_id' => $shortlink->id
            ]
        );

        return new Response('',Response::HTTP_CREATED);
    }


    /**
     * soft deletes a shortlink
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteShortlinkUrl(Request $request)
    {

        $validations = [
            'shortlink_id' => 'required|numeric',
        ];

        $enableCaptchaSitekey = config('captcha.enable');
        if ($enableCaptchaSitekey) {
            $validations['g-recaptcha-response'] = 'required|captcha';
        }

        Validator::make(
            $request->all(),
            $validations,
            [],
            []
        )->validate();

        $shortlink = Shortlink::findOrFail($request->input('shortlink_id'));

        // check user owns shortlink / has permission to edit the url
        if ($shortlink->user_id !== $request->user()->id) {
            //TODO: log that someone tried to delete someone elses shortlink redirect url?
            return new Response('', Response::HTTP_FORBIDDEN);
        }


        $shortlink->status_id = Shortlink::STATUS_DELETED;
        $shortlink->save();

        UserAction::logAction(
            $this->userId,
            ShortlinkActions::DELETED_SHORTLINK,
            [
                'shortlink_id' => $shortlink->id
            ]
        );

        return new Response('', Response::HTTP_OK);
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
