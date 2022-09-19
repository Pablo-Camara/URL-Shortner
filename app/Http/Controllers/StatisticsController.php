<?php

namespace App\Http\Controllers;

use App\Helpers\Actions\AuthActions;
use App\Models\UserAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Returns the total number of registered users (per register method - email/pwd / external social media login)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function totalRegisteredUsers(Request $request)
    {
        $since = $request->input('since', null);
        $until = $request->input('until', null);
        $view = $request->input('view', null);

        $availableViews = [
            [
                'name' => 'totals_by_action',
                'label' => 'Totais por Ação',
            ],
            [
                'name' => 'totals_by_action_and_day',
                'label' => 'Totais por Ação / Dia',
            ],
        ];

        if (!in_array($view, $availableViews)) {
            $view = $availableViews[0]['name'];
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



        $select = [
            'actions.name AS register_method',
            DB::raw('count(*) AS total')
        ];

        $groupBy = [
            'actions.name'
        ];

        switch ($view) {
            case 'by_day':
                array_unshift($select, DB::raw('user_actions.created_at_day AS day'));
                array_unshift($groupBy, DB::raw('user_actions.created_at_day'));
                break;
        }

        $results = UserAction::select($select)
        ->leftJoin('actions', 'user_actions.action_id', '=', 'actions.id')
        ->whereIn('actions.name', [
            AuthActions::REGISTERED,
            AuthActions::REGISTERED_WITH_GOOGLE,
            AuthActions::REGISTERED_WITH_FACEBOOK,
            AuthActions::REGISTERED_WITH_TWITTER,
            AuthActions::REGISTERED_WITH_LINKEDIN,
            AuthActions::REGISTERED_WITH_GITHUB
        ]);

        if ($since != null) {
            $results = $results->where('user_actions.created_at_day', '>=', $since);
        }

        if ($until != null) {
            $results = $results->where('user_actions.created_at_day', '<=', $until);
        }

        $results = $results->groupBy($groupBy)->get();

        return new Response(
            [
                'since' => $since,
                'until' => $until,
                'view'  => $view,
                'availableViews' => $availableViews,
                'search_results' => $results
            ]
        );

    }
}
