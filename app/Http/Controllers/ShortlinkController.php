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
use App\Models\PermissionGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

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

    /**
     * Returns the users links ( be him a guest or not )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myLinks(Request $request)
    {
        /**
         * @var PermissionGroup
         */
        $userPermissions = $request->user()->permissionGroup;

        $selectFields = [
            'id',
            'destination_email',
            'shortstring_id',
            'created_at'
        ];

        // all available order bys
        $availableOrderBys = [
            [
                'name' => 'id_desc',
                'label' => 'Mais recentes primeiro',
            ],
            [
                'name' => 'id_asc',
                'label' => 'Menos recentes primeiro',
            ],
            [
                'name' => 'total_views_desc',
                'label' => 'Com mais visualizações totais primeiro',
            ],
            [
                'name' => 'total_views_asc',
                'label' => 'Com menos visualizações totais primeiro',
            ],
            [
                'name' => 'total_unique_views_desc',
                'label' => 'Com mais visualizações únicas primeiro',
            ],
            [
                'name' => 'total_views_asc',
                'label' => 'Com menos visualizações únicas primeiro',
            ],
        ];

        // all order bys that the user is allowed to use
        $allowedOrderBys = [];

        $selectedOrderBy = $request->input('orderBy');

        $getAllowedOrderBysFunc = function ($allowedOrderBys) use ($availableOrderBys) {
            $result = [];
            foreach($availableOrderBys as $availableOrderBy) {
                if (
                    in_array($availableOrderBy['name'], $allowedOrderBys)
                ) {
                    array_push(
                        $result,
                        $availableOrderBy
                    );
                }
            }
            return $result;
        };

        // only logged in users should view the order by feature
        // but just in case an admin ever configures the permission
        // group for guests and allow guests to see shortlinks total views or unique views
        // we will add the "recency" order by
        // just like we will add the total/total unique views order by
        if (
            $this->isLoggedIn()
            ||
            $userPermissions->canViewShortlinksTotalViews()
            ||
            $userPermissions->canViewShortlinksTotalUniqueViews()
        ) {
            array_push($allowedOrderBys, 'id_desc');
            array_push($allowedOrderBys, 'id_asc');
        }

        $activeVisitsActionId = Action::where('name', '=', ShortlinkActions::VISITED_ACTIVE_SHORTLINK)->first();

        if (
            $activeVisitsActionId
        ) {
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

                array_push($allowedOrderBys, 'total_views_desc');
                array_push($allowedOrderBys, 'total_views_asc');
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

                array_push($allowedOrderBys, 'total_unique_views_desc');
                array_push($allowedOrderBys, 'total_views_asc');
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
         ->with(
            [
                'previousRedirectUrls' => function ($query) {
                    $query->select(['id', 'shortlink_id', 'url', 'created_at', 'updated_at']);
                }
            ]
         )
         ->where('status_id', '=', Shortlink::STATUS_ACTIVE);

         $userAllowedOrderBys = $getAllowedOrderBysFunc($allowedOrderBys);
         $orderBy = null;

         if (
            !in_array(
                $selectedOrderBy,
                $allowedOrderBys
            )
            ||
            empty($allowedOrderBys)
         ) {
            $selectedOrderBy = $availableOrderBys[0]['name'];
         }

         switch ($selectedOrderBy) {
            case 'id_desc':
                $orderBy = [
                    'shortlinks.id',
                    'DESC'
                ];
                break;
            case 'id_asc':
                $orderBy = [
                    'shortlinks.id',
                    'ASC'
                ];
                break;
            case 'total_views_desc':
                $orderBy = [
                    'total_views',
                    'DESC'
                ];
                break;
            case 'total_views_asc':
                $orderBy = [
                    'total_views',
                    'ASC'
                ];
                break;
            case 'total_unique_views_desc':
                $orderBy = [
                    'total_unique_views',
                    'DESC'
                ];
                break;
            case 'total_views_asc':
                $orderBy = [
                    'total_unique_views',
                    'ASC'
                ];
                break;
         }

         $userShortlinks = $userShortlinks->orderBy($orderBy[0],$orderBy[1]);

         $userShortlinks = $userShortlinks->paginate(3)
         ->through(function($shortlink){
            $shortlink->long_url = $shortlink->redirectUrl->url;
            $shortlink->shortlink = URL::to('/' . $shortlink->shortstring->shortstring);

            unset($shortlink->shortstring);
            unset($shortlink->shortstring_id);
            unset($shortlink->redirectUrl);
            return $shortlink;
        });


        UserAction::logAction($this->userId, ShortlinkActions::VIEWED_LINKS_LIST);

        $res = [
            'search_results' => $userShortlinks,
        ];

        if (!empty($userAllowedOrderBys)) {
            $res = array_merge(
                $res,
                [
                    'availableOrderBys' => $userAllowedOrderBys,
                    'orderBy' => $selectedOrderBy
                ]
            );
        }

        return new Response($res);
    }

    /**
     * Register custom shortlink
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerCustomShortlink(Request $request)
    {

        $user = $request->user();
        /**
         * @var PermissionGroup
         */
        $permissionGroup = $user->permissionGroup;

        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers

        $maxUrlLength = 2048;

        try {
            $validations = [
                // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                'long_url' => 'required|url|max:' . $maxUrlLength,
                'shortstring' => 'required|min:1|regex:/^[A-Za-z0-9-]*$/',
            ];

            $enableCaptchaSitekey = config('captcha.enable');
            if ($enableCaptchaSitekey) {
                $validations['g-recaptcha-response'] = 'required|captcha';
            }
            Validator::make(
                $request->all(),
                $validations,
                ['shortstring.regex' => 'Só é possível criar um link personalizado com letras, números e hífens.'],
                ['long_url' => 'URL']
            )->validate();

        } catch (ValidationException $ve) {
            if (
                $request->input('long_url', '')
                &&
                strlen($request->input('long_url')) > $maxUrlLength
            ) {
                UserAction::logAction(
                    $user->id,
                    ShortlinkActions::ATTEMPTED_TO_GENERATE_SHORTLINK_FOR_URL_THAT_IS_TOO_LONG
                );
            }
            throw $ve;
        } catch (\Throwable $th) {
            throw $th;
        }

        if ($request->getHost() === parse_url($request->input('long_url'))['host']) {
            UserAction::logAction(
                $user->id,
                ShortlinkActions::ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK
            );

            throw ValidationException::withMessages(['long_url' => 'Não é possível criar um link curto para este url.']);
        }

        // first we check if the user can create custom shortlinks
        if (false == $permissionGroup->canCreateCustomShortlinks()) {
            throw ValidationException::withMessages(['permissions' => 'Não tens permissões para criar links personalizados.']);
        }

        $shortstringText = $request->input('shortstring');

        $shortstring = Shortstring::where('shortstring', '=', $shortstringText)->first();

        if ($shortstring && !$shortstring->is_available) {
            UserAction::logAction($user->id, ShortlinkActions::ATTEMPTED_TO_REGISTER_UNAVAILABLE_SHORTSTRING);

            return new Response([
                //TODO: translate
                'message' => 'Este link não está disponível!'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if (
            strlen($shortstringText) <= 4
        ) {
            if (
                false == $permissionGroup->canCreateShortlinksWithSpecificLength(
                    strlen($shortstringText)
                )
            ) {
                throw ValidationException::withMessages(['permissions' => 'Não tens permissões para criar links com este tamanho ('.strlen($shortstringText) .').']);
            }

            $this->validateHasNotReachedLimitsForShortstringsWith4orLessOfLength(
                $user,
                $permissionGroup,
                strlen($shortstringText)
            );
        }

        if (
            strlen($shortstringText) >= 5
        ) {
            $this->validateHasNotReachedLimitsForShortstringsWith5orMoreOfLength(
                $user,
                $permissionGroup
            );
        }

        if (!$shortstring) {
            $shortstring = new Shortstring();
            $shortstring->is_custom = 1;
            $shortstring->is_available = 0;
            $shortstring->length = strlen($shortstringText);
            $shortstring->shortstring = $shortstringText;
            $shortstring->save();
        } else {
            $shortstring->is_available = 0;
            $shortstring->is_custom = 1;
            $shortstring->save();
        }

        $newShortlink = new Shortlink();
        $newShortlink->user_id = $user->id;
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

            UserAction::logAction($user->id, ShortlinkActions::REGISTERED_CUSTOM_SHORTLINK);
        } catch (\Throwable $th) {
            DB::rollBack();

            try {
                $shortstring->is_available = 1;
                $shortstring->save();
            } catch (\Throwable $th) {
                //TODO: try to log this
            }

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

        $user = $request->user();
        /**
         * @var PermissionGroup
         */
        $permissionGroup = $user->permissionGroup;

        // url validation, read below ( about max length )
        // https://stackoverflow.com/questions/417142/what-is-the-maximum-length-of-a-url-in-different-browsers

        $maxUrlLength = 2048;

        $customSize = $request->input('custom-size', null);
        try {

            $validations = [
                // long when validation fails - to know which users are trying to generate a shortlink from a really really long url
                'long_url' => 'required|url|max:' . $maxUrlLength,
                'destination_email' => 'email:rfc,dns',
                'custom-size' => 'integer|between:1,4'
            ];

            $enableCaptchaSitekey = config('captcha.enable');
            if ($enableCaptchaSitekey) {
                $validations['g-recaptcha-response'] = 'required|captcha';
            }

            Validator::make(
                $request->all(),
                $validations,
                [
                    'custom-size.between' => 'Tamanho inválido.',
                    'custom-size.integer' => 'Tamanho inválido.',
                ],
                ['long_url' => 'URL']
            )->validate();

        } catch (ValidationException $ve) {
            if (
                $request->input('long_url', '')
                &&
                strlen($request->input('long_url')) > $maxUrlLength
            ) {
                UserAction::logAction(
                    $user->id,
                    ShortlinkActions::ATTEMPTED_TO_GENERATE_SHORTLINK_FOR_URL_THAT_IS_TOO_LONG
                );
            }
            throw $ve;
        } catch (\Throwable $th) {
            throw $th;
        }

        if ($request->getHost() === parse_url($request->input('long_url'))['host']) {
            UserAction::logAction(
                $user->id,
                ShortlinkActions::ATTEMPTED_TO_CREATE_SHORTLINK_FOR_SHORTLINK
            );

            throw ValidationException::withMessages(['long_url' => 'Não é possível criar um link curto para este url.']);
        }

        $useBaseConvert = true;
        if (!empty($customSize)) {
            $useBaseConvert = false;
        }

        $this->validateHasNotReachedLimitsForShortstringsWith5orMoreOfLength(
            $user,
            $permissionGroup
        );

        if ($useBaseConvert) {
            $nextAvailableShortstring = $this->tryGeneratingShortstringWithBaseConvert(5);
        } else {

            if (
                false == $permissionGroup->canCreateShortlinksWithSpecificLength(
                    $customSize
                )
            ) {
                throw ValidationException::withMessages(['permissions' => 'Não tens permissões para criar links com este tamanho ('.$customSize .').']);
            }

            $this->validateHasNotReachedLimitsForShortstringsWith4orLessOfLength(
                $user,
                $permissionGroup,
                $customSize
            );

            $useRandom = config('app.use_random_for_premium_urls');

            $nextAvailableShortstring = Shortstring::where(
                'is_available', '=', 1
            )->where(
                'length', '=', $customSize
            );

            if ($useRandom) {
                $nextAvailableShortstring->inRandomOrder();
            }

            $nextAvailableShortstring = $nextAvailableShortstring->first();
        }


        if (!$nextAvailableShortstring) {
            // this should never happen
            // but in case we ever run out of shortstrings/available links
            // we should be notified.

            //TODO: Send email notification.

            UserAction::logAction($user->id, ShortlinkActions::FOUND_NO_AVAILABLE_SHORTSTRING);


            return new Response(
                [
                    'message' => 'Não encontramos um link disponível com estas configurações.'
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        $newShortlink = new Shortlink();
        $newShortlink->user_id = $user->id;
        $newShortlink->shortstring_id = $nextAvailableShortstring->id;

        if (
            !is_null($request->input('destination_email'))
            &&
            $permissionGroup->canSendShortlinkByEmailWhenGenerating()
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


            UserAction::logAction(
                $newShortlink->user_id,
                $useBaseConvert ? ShortlinkActions::GENERATED_SHORTLINK_WITH_BC : ShortlinkActions::GENERATED_SHORTLINK_WITH_PRESEEDED_STRING,
                [
                    'shortlink_id' => $newShortlink->id
                ]
            );

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        DB::commit();

        if (
            !is_null($newShortlink->destination_email)
            &&
            $permissionGroup->canSendShortlinkByEmailWhenGenerating()
        ) {
            Mail::to(
                $newShortlink->destination_email
            )->queue(new ShortlinkReady($newShortlink));

            UserAction::logAction($user->id, ShortlinkActions::SENT_SHORTLINK_TO_EMAIL);
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
         * @var PermissionGroup
         */
        $userPermissions = $request->user()->permissionGroup;

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

        return new Response([
            'previous_redirect_urls' => $shortlink->previousRedirectUrls
        ],Response::HTTP_CREATED);
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
     * @return string
     */
    private function generateShortstringWithBaseConvert(int $currentAutoincrementValue, $minimumShortstringLength) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $totalAlphabetChars = strlen($alphabet);


        $totalRecordCombinationsFrom1to4 = (36**1)+(36**2)+(36**3)+(36**4);

        // not to miss combinations due to having preseeded combinations
        // with length 1 to 4, they must be seeded.
        if ($currentAutoincrementValue >= $totalRecordCombinationsFrom1to4) {
            $currentAutoincrementValue = $currentAutoincrementValue - $totalRecordCombinationsFrom1to4;
        }
        $shortstring = base_convert(
            ($currentAutoincrementValue - 1 /* minus 1, to not skip first convertion ( 0 ) */) + ($totalAlphabetChars**($minimumShortstringLength-1)),
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
    private function tryGeneratingShortstringWithBaseConvert($minimumShortstringLength) {
        $shortstringCreated = false;
        $totalAttempts = 0;
        $nextAvailableShortstring = null;
        while ($shortstringCreated == false) {
            try {
                $shortstringsTableStatus = DB::select(
                    "SHOW TABLE STATUS LIKE 'shortstrings'"
                );
                $currAutoincrementVal = $shortstringsTableStatus[0]->Auto_increment;
                $shortstringToAdd = $this->generateShortstringWithBaseConvert($currAutoincrementVal, $minimumShortstringLength);

                $nextAvailableShortstring = new Shortstring();
                $nextAvailableShortstring->shortstring = $shortstringToAdd;

                // we make it available
                // until we use it
                // and make it unavailable
                $nextAvailableShortstring->is_custom = 0;
                $nextAvailableShortstring->length = strlen($shortstringToAdd);
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


    private function validateHasNotReachedLimitsForShortstringsWith5orMoreOfLength(
        $user,
        $permissionGroup
    ) {
        $minimumShortstringLength = 5;
        /** Total Limit with 5 of more of length */
        $totalShortlinksWith5orMoreOfLength = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '>=', $minimumShortstringLength)
                                                ->count();

        $permissionName = 'max_shortlinks_with_'.$minimumShortstringLength.'_or_more_of_length';
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinksWith5orMoreOfLength >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links curtos (não personalizados) que podes gerar.']);
        }
        /** --------------- */


        /** Total Limit with 5 of more of length - YEARLY LIMIT */
        $totalShortlinksThisYearWith5orMoreOfLength = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '>=', $minimumShortstringLength)
                                                ->where('shortlinks.created_at_day', '>=', Carbon::now()->firstOfYear())
                                                ->where('shortlinks.created_at_day', '<=', Carbon::now()->lastOfYear())
                                                ->count();

        $permissionName = 'max_shortlinks_per_year_with_'.$minimumShortstringLength.'_or_more_of_length';
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinksThisYearWith5orMoreOfLength >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links curtos (não personalizados) que podes gerar por ano.']);
        }
        /** --------------- */


         /** Total Limit with 5 of more of length - MONTHLY LIMIT */
         $totalShortlinksThisMonthWith5orMoreOfLength = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
         ->where('user_id', '=', $user->id)
         ->where('shortstrings.length', '>=', $minimumShortstringLength)
         ->where('shortlinks.created_at_day', '>=', Carbon::now()->firstOfMonth())
         ->where('shortlinks.created_at_day', '<=', Carbon::now()->lastOfMonth())
         ->count();

        $permissionName = 'max_shortlinks_per_month_with_'.$minimumShortstringLength.'_or_more_of_length';
        if (
        !is_null($permissionGroup->$permissionName)
        &&
        $totalShortlinksThisMonthWith5orMoreOfLength >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links curtos (não personalizados) que podes gerar por mês.']);
        }
        /** --------------- */


        /** Total Limit with 5 of more of length - DAILY LIMIT */
        $totalShortlinksTodayWith5orMoreOfLength = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '>=', $minimumShortstringLength)
                                                ->where('shortlinks.created_at_day', '=', Carbon::now()->toDateString())
                                                ->count();

        $permissionName = 'max_shortlinks_per_day_with_'.$minimumShortstringLength.'_or_more_of_length';
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinksTodayWith5orMoreOfLength >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links curtos (não personalizados) que podes gerar por dia.']);
        }
        /** --------------- */
    }


    private function validateHasNotReachedLimitsForShortstringsWith4orLessOfLength(
        $user,
        $permissionGroup,
        $shortstringLength
    ) {
        /** Total Limit */
        $totalShortlinks = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '=', $shortstringLength)
                                                ->count();

        $permissionName = 'max_shortlinks_with_length_'.$shortstringLength;
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinks >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links que podes gerar com este tamanho (' . $shortstringLength . ')']);
        }
        /** --------------- */


        /** YEARLY LIMIT */
        $totalShortlinksThisYear = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '=', $shortstringLength)
                                                ->where('shortlinks.created_at_day', '>=', Carbon::now()->firstOfYear())
                                                ->where('shortlinks.created_at_day', '<=', Carbon::now()->lastOfYear())
                                                ->count();

        $permissionName = 'max_shortlinks_per_year_with_length_'.$shortstringLength;
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinksThisYear >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links que podes gerar por ano com este tamanho ('.$shortstringLength.').']);
        }
        /** --------------- */


         /** MONTHLY LIMIT */
         $totalShortlinksThisMonth = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
         ->where('user_id', '=', $user->id)
         ->where('shortstrings.length', '=', $shortstringLength)
         ->where('shortlinks.created_at_day', '>=', Carbon::now()->firstOfMonth())
         ->where('shortlinks.created_at_day', '<=', Carbon::now()->lastOfMonth())
         ->count();

        $permissionName = 'max_shortlinks_per_month_with_length_'.$shortstringLength;
        if (
        !is_null($permissionGroup->$permissionName)
        &&
        $totalShortlinksThisMonth >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links que podes gerar por mês com este tamanho ('.$shortstringLength.').']);
        }
        /** --------------- */


        /** DAILY LIMIT */
        $totalShortlinksToday = Shortlink::leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
                                                ->where('user_id', '=', $user->id)
                                                ->where('shortstrings.length', '=', $shortstringLength)
                                                ->where('shortlinks.created_at_day', '=', Carbon::now()->toDateString())
                                                ->count();

        $permissionName = 'max_shortlinks_per_day_with_length_'.$shortstringLength;
        if (
            !is_null($permissionGroup->$permissionName)
            &&
            $totalShortlinksToday >= $permissionGroup->$permissionName
        ) {
            throw ValidationException::withMessages([$permissionName => 'Atingiste o limite total de links que podes gerar por dia com este tamanho ('.$shortstringLength.').']);
        }
        /** --------------- */
    }
}
