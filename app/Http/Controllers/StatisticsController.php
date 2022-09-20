<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\ShortlinkActions;
use App\Models\UserAction;
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
            /*'shortlinksWithMostViews' => [
                ShortlinkActions::VIS
            ]*/
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
            $currDateRange = UserAction::select([
                DB::raw('COALESCE(MIN(created_at_day), CURRENT_DATE) AS first_user_action_date'),
                DB::raw('COALESCE(MAX(created_at_day), CURRENT_DATE) AS last_user_action_date')
            ])->get();

            $since = $currDateRange[0]->first_user_action_date;
            $until = $currDateRange[0]->last_user_action_date;
        }

        $colsToTranslateValues = [
            __('admin-panel.action')
        ];



        $select = null;
        $groupBy = null;

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
        }

        $results = UserAction::select($select)
        ->leftJoin('actions', 'user_actions.action_id', '=', 'actions.id')
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

        $results = $results->get()->toArray();

        $results = array_map(
            function($row) use ($colsToTranslateValues) {
                foreach($colsToTranslateValues as $colName) {
                    if (!isset($row[$colName])) {
                        continue;
                    }

                    $row[$colName] = __('admin-panel.' . $row[$colName]);
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
