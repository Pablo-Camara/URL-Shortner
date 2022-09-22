<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\ShortlinkActions;
use App\Helpers\Responses\AuthResponses;
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
        if (!$request->user()->isAdmin()) {
            return AuthResponses::notAuthorized();
        }

        $currentView = $request->input('currentView', null);

        if (empty($currentView)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $viewModelMap = [
            'appUsageByDevices' => UserDevice::class,
            'appUsageByAction' => UserAction::class,
            'totalRegisteredUsers' => UserAction::class,
            'totalShortlinksGenerated' => UserAction::class,
            'totalTrafficReceivedInShortlinks' => UserAction::class,
            'totalUserLogins' => UserAction::class,
        ];

        if (!in_array($currentView, array_keys($viewModelMap))) {
            return new Response('View model not found / not set', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $model = app($viewModelMap[$currentView]);
        $table = $model->getTable();

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
                ShortlinkActions::VISITED_ACTIVE_SHORTLINK,
                ShortlinkActions::VISITED_DELETED_SHORTLINK,
                ShortlinkActions::VISITED_SUSPENDED_SHORTLINK,
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

        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $selectedGroupBy = $request->input('groupBy', null);
        $selectedOrderBy = $request->input('orderBy', null);

        $availableGroupBys = [
            [
                'name' => 'total',
                'label' => 'Total',
            ],
            [
                'name' => 'total_by_all_devices',
                'label' => 'Total',
            ],
            [
                'name' => 'total_by_all_devices_and_day',
                'label' => 'Total por Dia',
            ],
            [
                'name' => 'totals_by_browser',
                'label' => 'Total por Navegador',
            ],
            [
                'name' => 'totals_by_browser_and_day',
                'label' => 'Total por Navegador + Dia',
            ],
            [
                'name' => 'total_by_device_width_and_height',
                'label' => 'Total por Largura/Altura de Ecrã',
            ],
            [
                'name' => 'total_by_device_width_and_height_and_day',
                'label' => 'Total por Largura/Altura de Ecrã + Dia',
            ],
            [
                'name' => 'total_by_device',
                'label' => 'Total por Dispositivo',
            ],
            [
                'name' => 'total_by_device_and_day',
                'label' => 'Total por Dispositivo + Dia',
            ],
            [
                'name' => 'total_by_platform',
                'label' => 'Total por Plataforma',
            ],
            [
                'name' => 'total_by_platform_and_day',
                'label' => 'Total por Plataforma + Dia',
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
                'name' => 'totals_by_shortlink_and_action',
                'label' => 'Total por Shortlink + Ação',
            ],
            [
                'name' => 'totals_by_shortlink_and_action_and_day',
                'label' => 'Total por Shortlink + Ação + Dia',
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

        $availableOrderBys = [
            [
                'name' => 'total_desc',
                'label' => 'Total (descendente)',
            ],
            [
                'name' => 'total_asc',
                'label' => 'Total (ascendente)',
            ],
            [
                'name' => 'day_desc',
                'label' => 'Dia (descendente)',
            ],
            [
                'name' => 'day_asc',
                'label' => 'Dia (ascendente)',
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
            ],
            'appUsageByDevices' => [
                'total_by_all_devices', 'total_by_all_devices_and_day', 'totals_by_browser',
                'totals_by_browser_and_day', 'total_by_device_width_and_height',
                'total_by_device_width_and_height_and_day', 'total_by_device',
                'total_by_device_and_day', 'total_by_platform', 'total_by_platform_and_day'
            ],
            'appUsageByAction' => [
                'total', 'totals_by_day', 'totals_by_action', 'totals_by_action_and_day',
                'totals_by_user_type', 'totals_by_user_type_and_day', 'totals_by_user', 'totals_by_user_and_day',
                'totals_by_shortlink', 'totals_by_shortlink_and_day', 'totals_by_shortlink_and_action', 'totals_by_shortlink_and_action_and_day'
            ]
        ];

        //depending on the group by, we may have certain order bys
        $groupByOrderBysMap = [
            'totals_by_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_action' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_action_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_user_type' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_user_type_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_user' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_user_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_shortlink' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_shortlink_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_shortlink_and_action' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_shortlink_and_action_and_day' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_user_type' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_user_type_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'total_by_all_devices' => [
                'total_desc', 'total_asc'
            ],
            'total_by_all_devices_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'totals_by_browser' => [
                'total_desc', 'total_asc'
            ],
            'totals_by_browser_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'total_by_device_width_and_height' => [
                'total_desc', 'total_asc'
            ],
            'total_by_device_width_and_height_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'total_by_device' => [
                'total_desc', 'total_asc'
            ],
            'total_by_device_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
            'total_by_platform' => [
                'total_desc', 'total_asc'
            ],
            'total_by_platform_and_day' => [
                'total_desc', 'total_asc', 'day_desc', 'day_asc'
            ],
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

        $availableOrderBys = array_values(
            array_filter(
                $availableOrderBys,
                function ($orderBy) use ($selectedGroupBy, $groupByOrderBysMap) {
                    if (!isset($groupByOrderBysMap[$selectedGroupBy])) {
                        return false;
                    }
                    return in_array($orderBy['name'], $groupByOrderBysMap[$selectedGroupBy]);
                }
            )
        );

        if (
            isset($groupByOrderBysMap[$selectedGroupBy])
            &&
            !in_array(
                $selectedOrderBy,
                array_map(
                    function ($orderByDataArr) {
                        return $orderByDataArr['name'];
                    },
                    $availableOrderBys
                )
            )
        ) {
            $selectedOrderBy = $groupByOrderBysMap[$selectedGroupBy][0];
        }

        if ($since == null && $until == null) {
            // get date of first and last user action, for default filter values
            $currDateRange = $model::select([
                DB::raw('COALESCE(MIN(created_at_day), CURRENT_DATE) AS first_date'),
                DB::raw('COALESCE(MAX(created_at_day), CURRENT_DATE) AS last_date')
            ])->get();

            $since = $currDateRange[0]->first_date;
            $until = $currDateRange[0]->last_date;
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
            case 'total_by_all_devices':
                $select = [
                    DB::raw($table . '.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw($table . '.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw($table . '.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw($table . '.device AS `'.__('admin-panel.device').'`'),
                    DB::raw($table . '.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    $table . '.browser',
                    $table . '.device_height',
                    $table . '.device_width',
                    $table . '.device',
                    $table . '.platform'
                ];
                break;
            case 'total_by_all_devices_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw($table . '.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw($table . '.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw($table . '.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw($table . '.device AS `'.__('admin-panel.device').'`'),
                    DB::raw($table . '.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    $table . '.browser',
                    $table . '.device_height',
                    $table . '.device_width',
                    $table . '.device',
                    $table . '.platform'
                ];
                break;
            case 'totals_by_browser':
                $select = [
                    DB::raw($table . '.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    $table . '.browser',
                ];
                break;
            case 'totals_by_browser_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw($table . '.browser AS `'.__('admin-panel.browser').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    $table . '.browser'
                ];
                break;
            case 'total_by_device_width_and_height':
                $select = [
                    DB::raw($table . '.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw($table . '.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    $table . '.device_height',
                    $table . '.device_width',
                ];
                break;
            case 'total_by_device_width_and_height_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw($table . '.device_height AS `'.__('admin-panel.device_height').'`'),
                    DB::raw($table . '.device_width AS `'.__('admin-panel.device_width').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    $table . '.device_height',
                    $table . '.device_width',
                ];
                break;

            case 'total_by_device':
                $select = [
                    DB::raw($table . '.device AS `'.__('admin-panel.device').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    $table . '.device'
                ];
                break;
            case 'total_by_device_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw($table . '.device AS `'.__('admin-panel.device').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    $table . '.device'
                ];
                break;

            case 'total_by_platform':
                $select = [
                    DB::raw($table . '.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    $table . '.platform'
                ];
                break;
            case 'total_by_platform_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw($table . '.platform AS `'.__('admin-panel.platform').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    $table . '.platform'
                ];
                break;

            case 'totals_by_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day')
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
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('actions.name AS `'.__('admin-panel.action').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
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
                break;
            case 'totals_by_shortlink_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('shortstrings.shortstring AS `'.__('admin-panel.shortlink').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    DB::raw('shortstrings.shortstring')
                ];
                break;
            case 'totals_by_shortlink_and_action':
                $select = [
                    DB::raw('shortstrings.shortstring AS `'.__('admin-panel.shortlink').'`'),
                    DB::raw('actions.name AS `'.__('admin-panel.action').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw('shortstrings.shortstring'),
                    'actions.name'
                ];
                break;
            case 'totals_by_shortlink_and_action_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('shortstrings.shortstring AS `'.__('admin-panel.shortlink').'`'),
                    DB::raw('actions.name AS `'.__('admin-panel.action').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    DB::raw('shortstrings.shortstring'),
                    'actions.name'
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
                break;
            case 'totals_by_user_type_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('CASE WHEN users.guest = 1 THEN \'guest_users\' ELSE \'registered_users\' END AS `'.__('admin-panel.user-type').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    DB::raw('users.guest')
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
                break;
            case 'totals_by_user_and_day':
                $select = [
                    DB::raw($table . '.created_at_day AS `'.__('admin-panel.day').'`'),
                    DB::raw('users.id AS `'.__('admin-panel.user-id').'`'),
                    DB::raw('count(*) AS `'.__('admin-panel.total').'`')
                ];
                $groupBy = [
                    DB::raw($table . '.created_at_day'),
                    DB::raw('users.id')
                ];
                break;
        }

        switch ($selectedOrderBy) {
            case 'total_desc':
                $orderBy = [
                    DB::raw('count(*)'),
                    'DESC'
                ];
                break;
            case 'total_asc':
                $orderBy = [
                    DB::raw('count(*)'),
                    'ASC'
                ];
                break;
            case 'day_desc':
                $orderBy = [
                    DB::raw($table . '.created_at_day'),
                    'DESC'
                ];
                break;
            case 'day_asc':
                $orderBy = [
                    DB::raw($table . '.created_at_day'),
                    'ASC'
                ];
                break;
        }

        $results = $model::select($select);

        // joins
        switch ($table) {
            case 'user_actions':
                $results = $results->leftJoin('users', 'user_actions.user_id', '=', 'users.id')
                ->leftJoin('actions', 'user_actions.action_id', '=', 'actions.id')
                ->leftJoin('shortlinks', 'user_actions.shortlink_id', '=', 'shortlinks.id')
                ->leftJoin('shortstrings', 'shortlinks.shortstring_id', '=', 'shortstrings.id');
                break;
        }

        // custom wheres ( will probably change later how i handle wheres)
        switch ($table) {
            case 'user_actions':
                if (isset($viewActionsMap[$currentView])) {
                    $results = $results->whereIn('actions.name', $viewActionsMap[$currentView]);
                }

                if (
                    $currentView === 'appUsageByAction'
                    &&
                    in_array(
                        $selectedGroupBy,
                        [
                            'totals_by_shortlink',
                            'totals_by_shortlink_and_day',
                            'totals_by_shortlink_and_action',
                            'totals_by_shortlink_and_action_and_day'
                        ]
                    )
                ) {
                    $results = $results->whereNotNull($table . '.shortlink_id');
                }
                break;
        }


        if ($since != null) {
            $results = $results->where($table . '.created_at_day', '>=', $since);
        }

        if ($until != null) {
            $results = $results->where($table . '.created_at_day', '<=', $until);
        }

        if ($groupBy != null) {
            $results = $results->groupBy($groupBy);
        }

        if ($orderBy != null) {
            $results = $results->orderBy($orderBy[0], $orderBy[1]);
        }

        $results = $results->paginate(100)->toArray();

        $results['data'] = array_map(
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
            $results['data']
        );


        $resData = [
            'since' => $since,
            'until' => $until,
            'groupBy'  => $selectedGroupBy,
            'availableGroupBys' => $availableGroupBys,
            'search_results' => $results
        ];

        if (!empty($availableOrderBys)) {
            $resData['orderBy'] = $selectedOrderBy;
            $resData['availableOrderBys'] = $availableOrderBys;
        }

        return new Response(
            $resData
        );

    }

}
