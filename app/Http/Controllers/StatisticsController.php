<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\ShortlinkActions;
use App\Models\UserAction;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generic(Request $request)
    {
        $currentView = $request->input('currentView', null);

        if (empty($currentView)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($currentView === 'appUsageByDevices') {
            return $this->userDevices($request);
        }

        $viewActionsMap = [
            'totalRegisteredUsers' => [
                AuthActions::REGISTERED,
                AuthActions::REGISTERED_WITH_GOOGLE,
                AuthActions::REGISTERED_WITH_FACEBOOK,
                AuthActions::REGISTERED_WITH_TWITTER,
                AuthActions::REGISTERED_WITH_LINKEDIN,
                AuthActions::REGISTERED_WITH_GITHUB
            ],
            'totalShortlinksGenerated' => [
                ShortlinkActions::GENERATED_SHORTLINK_WITH_BC,
                ShortlinkActions::GENERATED_SHORTLINK_WITH_PRESEEDED_STRING,
            ],
            'totalTrafficReceivedInShortlinks' => [
                ShortlinkActions::VISITED_ACTIVE_SHORTLINK
            ],
            'totalUserLogins' => [
                AuthActions::LOGGED_IN,
                AuthActions::LOGGED_IN_WITH_GOOGLE,
                AuthActions::LOGGED_IN_WITH_FACEBOOK,
                AuthActions::LOGGED_IN_WITH_TWITTER,
                AuthActions::LOGGED_IN_WITH_LINKEDIN,
                AuthActions::LOGGED_IN_WITH_GITHUB,
            ]
        ];

        if (!isset($viewActionsMap[$currentView])) {
            return new Response('View "' . $currentView . '" not found', Response::HTTP_NOT_FOUND);
        }

        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $selectedGroupBy = $request->input('groupBy', null);

        $availableGroupBys = [
            [
                'name' => 'total',
                'label' => 'Total',
            ],
            [
                'name' => 'totals_by_day',
                'label' => 'Total por Dia',
            ],
            [
                'name' => 'totals_by_action',
                'label' => 'Total por Ação',
            ],
            [
                'name' => 'totals_by_action_and_day',
                'label' => 'Total por Ação + Dia',
            ],
            [
                'name' => 'totals_by_shortlink',
                'label' => 'Total por Shortlink',
            ],
            [
                'name' => 'totals_by_shortlink_and_day',
                'label' => 'Total por Shortlink + Dia',
            ],
            [
                'name' => 'totals_by_user_type',
                'label' => 'Total por Tipo de Utilizador',
            ],
            [
                'name' => 'totals_by_user_type_and_day',
                'label' => 'Total por Tipo de Utilizador + Dia',
            ],
            [
                'name' => 'totals_by_user',
                'label' => 'Total por Utilizador',
            ],
            [
                'name' => 'totals_by_user_and_day',
                'label' => 'Total por Utilizador + Dia',
            ],
        ];


        $viewAvailableGroupBysMap = [
            'totalRegisteredUsers' => [
                'total', 'totals_by_day', 'totals_by_action', 'totals_by_action_and_day'
            ],
            'totalShortlinksGenerated' => [
                'total', 'totals_by_day', 'totals_by_action', 'totals_by_action_and_day',
                'totals_by_user_type', 'totals_by_user_type_and_day', 'totals_by_user', 'totals_by_user_and_day'
            ],
            'totalTrafficReceivedInShortlinks' => [
                'total', 'totals_by_day', 'totals_by_action', 'totals_by_action_and_day',
                'totals_by_shortlink', 'totals_by_shortlink_and_day', 'totals_by_user_type', 'totals_by_user_type_and_day',
                'totals_by_user', 'totals_by_user_and_day'
            ],
            'totalUserLogins' => [
                'total', 'totals_by_day', 'totals_by_action', 'totals_by_action_and_day'
            ]
        ];

        $availableGroupBys = array_values(
            array_filter(
                $availableGroupBys,
                function ($groupBy) use ($currentView, $viewAvailableGroupBysMap) {
                    return in_array($groupBy['name'], $viewAvailableGroupBysMap[$currentView]);
                }
            )
        );

        if (
            !in_array(
                $selectedGroupBy,
                array_map(
                    function ($groupByDataArr) {
                        return $groupByDataArr['name'];
                    },
                    $availableGroupBys
                )
            )
        ) {
            $selectedGroupBy = $availableGroupBys[0]['name'];
        }

        if ($since == null && $until == null) {
            // get date of first and last user action, for default filter values
            $currDateRange = UserAction::select([
                DB::raw('COALESCE(MIN(created_at_day), CURRENT_DATE) AS first_user_action_date'),
                DB::raw('COALESCE(MAX(created_at_day), CURRENT_DATE) AS last_user_action_date')
            ])->get();

            $since = $currDateRange[0]->first_user_action_date;
            $until = $currDateRange[0]->last_user_action_date;
        }

        $colsToTranslateValues = [
            __('admin-panel.action'),
            __('admin-panel.user-type')
        ];

        $colsToTransformValues = [
            __('admin-panel.shortlink') => function ($shortstring) {
                return url('/' . $shortstring);
            }
        ];


        $select = null;
        $groupBy = null;
        $orderBy = null;

        switch ($selectedGroupBy) {
            case 'total':
                $select = [
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = null;
                break;
            case 'totals_by_day':
                $select = [
                    DB::raw('user_actions.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('user_actions.created_at_day')
                ];
                break;
            case 'totals_by_action':
                $select = [
                    DB::raw('actions.name AS `'.__('admin-panel.action').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    'actions.name'
                ];
                break;
            case 'totals_by_action_and_day':
                $select = [
                    DB::raw('user_actions.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('actions.name AS `'.__('admin-panel.action').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('user_actions.created_at_day'),
                    'actions.name'
                ];
                break;
            case 'totals_by_shortlink':
                $select = [
                    DB::raw('shortstrings.shortstring AS `'.__('admin-panel.shortlink').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('shortstrings.shortstring')
                ];

                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
            case 'totals_by_user_type':
                $select = [
                    DB::raw('CASE WHEN users.guest = 1 THEN \'guest_users\' ELSE \'registered_users\' END AS `'.__('admin-panel.user-type').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('users.guest')
                ];

                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
            case 'totals_by_user_type_and_day':
                $select = [
                    DB::raw('user_actions.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('CASE WHEN users.guest = 1 THEN \'guest_users\' ELSE \'registered_users\' END AS `'.__('admin-panel.user-type').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('user_actions.created_at_day'),
                    DB::raw('users.guest')
                ];

                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
            case 'totals_by_user':
                $select = [
                    DB::raw('users.id AS `'.__('admin-panel.user-id').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('users.id')
                ];

                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
            case 'totals_by_user_and_day':
                $select = [
                    DB::raw('user_actions.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('users.id AS `'.__('admin-panel.user-id').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('user_actions.created_at_day'),
                    DB::raw('users.id')
                ];

                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
        }

        $results = UserAction::select($select)
        ->leftJoin('users', 'user_actions.user_id', '=', 'users.id')
        ->leftJoin('actions', 'user_actions.action_id', '=', 'actions.id')
        ->leftJoin('shortlinks', 'user_actions.shortlink_id', '=', 'shortlinks.id')
        ->leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id')
        ->whereIn('actions.name', $viewActionsMap[$currentView]);

        if ($since != null) {
            $results = $results->where('user_actions.created_at_day', '>=', $since);
        }

        if ($until != null) {
            $results = $results->where('user_actions.created_at_day', '<=', $until);
        }

        if ($groupBy != null) {
            $results = $results->groupBy($groupBy);
        }

        if ($orderBy != null) {
            $results = $results->orderBy($orderBy[0], $orderBy[1]);
        }

        $results = $results->get()->toArray();

        $results = array_map(
            function($row) use ($colsToTranslateValues, $colsToTransformValues) {
                foreach($colsToTranslateValues as $colName) {
                    if (!isset($row[$colName])) {
                        continue;
                    }

                    $row[$colName] = __('admin-panel.' . $row[$colName]);
                }

                foreach($colsToTransformValues as $colName => $transformFunction) {
                    if (!isset($row[$colName])) {
                        continue;
                    }

                    $row[$colName] = $transformFunction($row[$colName]);
                }
                return $row;
            },
            $results
        );

        return new Response(
            [
                'since' => $since,
                'until' => $until,
                'groupBy'  => $selectedGroupBy,
                'availableGroupBys' => $availableGroupBys,
                'search_results' => $results
            ]
        );

    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userDevices(Request $request)
    {
        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $selectedGroupBy = $request->input('groupBy', null);

        $availableGroupBys = [
            [
                'name' => 'total',
                'label' => 'Total',
            ],
            [
                'name' => 'totals_by_day',
                'label' => 'Total por Dia',
            ],
        ];

        if (
            !in_array(
                $selectedGroupBy,
                array_map(
                    function ($groupByDataArr) {
                        return $groupByDataArr['name'];
                    },
                    $availableGroupBys
                )
            )
        ) {
            $selectedGroupBy = $availableGroupBys[0]['name'];
        }

        if ($since == null && $until == null) {
            // get date of first and last user action, for default filter values
            $currDateRange = UserDevice::select([
                DB::raw('COALESCE(MIN(created_at_day), CURRENT_DATE) AS first_date'),
                DB::raw('COALESCE(MAX(created_at_day), CURRENT_DATE) AS last_date')
            ])->get();

            $since = $currDateRange[0]->first_date;
            $until = $currDateRange[0]->last_date;
        }

        $colsToTranslateValues = [];

        $colsToTransformValues = [];

        $select = null;
        $groupBy = null;
        $orderBy = null;

        switch ($selectedGroupBy) {
            case 'total':
                $select = [
                    DB::raw('user_devices.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw('user_devices.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw('user_devices.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw('user_devices.device AS `'.__('admin-panel.device').'`'),
                    DB::raw('user_devices.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    'user_devices.browser',
                    'user_devices.device_height',
                    'user_devices.device_width',
                    'user_devices.device',
                    'user_devices.platform'
                ];
                break;
            case 'totals_by_day':
                $select = [
                    DB::raw('user_devices.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('user_devices.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw('user_devices.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw('user_devices.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw('user_devices.device AS `'.__('admin-panel.device').'`'),
                    DB::raw('user_devices.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('user_devices.created_at_day'),
                    'user_devices.browser',
                    'user_devices.device_height',
                    'user_devices.device_width',
                    'user_devices.device',
                    'user_devices.platform'
                ];
                break;
        }

        $results = UserDevice::select($select);

        if ($since != null) {
            $results = $results->where('user_devices.created_at_day', '>=', $since);
        }

        if ($until != null) {
            $results = $results->where('user_devices.created_at_day', '<=', $until);
        }

        if ($groupBy != null) {
            $results = $results->groupBy($groupBy);
        }

        if ($orderBy != null) {
            $results = $results->orderBy($orderBy[0], $orderBy[1]);
        }

        $results = $results->get()->toArray();

        $results = array_map(
            function($row) use ($colsToTranslateValues, $colsToTransformValues) {
                foreach($colsToTranslateValues as $colName) {
                    if (!isset($row[$colName])) {
                        continue;
                    }

                    $row[$colName] = __('admin-panel.' . $row[$colName]);
                }

                foreach($colsToTransformValues as $colName => $transformFunction) {
                    if (!isset($row[$colName])) {
                        continue;
                    }

                    $row[$colName] = $transformFunction($row[$colName]);
                }
                return $row;
            },
            $results
        );

        return new Response(
            [
                'since' => $since,
                'until' => $until,
                'groupBy'  => $selectedGroupBy,
                'availableGroupBys' => $availableGroupBys,
                'search_results' => $results
            ]
        );

    }
}
