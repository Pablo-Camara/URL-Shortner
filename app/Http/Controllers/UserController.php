<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\AuthResponses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return AuthResponses::notAuthorized();
        }

        $colsToTranslateValues = [];


        $transformBinaryToText = function ($binary) {
            return (bool)$binary ? __('admin-panel.yes') : __('admin-panel.no');
        };

        $colsToTransformValues = [
            __('admin-panel.has-verified-email') => $transformBinaryToText,
            __('admin-panel.has-password') => $transformBinaryToText
        ];


        $results = User::select([
            DB::raw('users.id AS `'.__('admin-panel.user-id').'`'),
            DB::raw('users.name AS `'.__('admin-panel.name').'`'),
            DB::raw('users.email AS `'.__('admin-panel.email').'`'),
            DB::raw('CONCAT(permission_groups.name, \'(\', permission_groups.id, \')\') AS `'.__('admin-panel.permission-group').'`'),
            DB::raw('CASE WHEN users.email_verified_at IS NOT NULL THEN 1 ELSE 0 END AS `'.__('admin-panel.has-verified-email').'`'),
            DB::raw('CASE WHEN users.password IS NOT NULL THEN 1 ELSE 0 END AS `'.__('admin-panel.has-password').'`'),
            DB::raw('users.created_at AS `'.__('admin-panel.created_at').'`'),
            DB::raw('users.updated_at AS `'.__('admin-panel.updated_at').'`'),
        ])->leftJoin(
            'permission_groups', 'permission_groups.id', '=', 'users.permission_group_id'
        )->where('guest', '=', 0);


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

        return new Response(
            [
                'search_results' => $results
            ]
        );

    }

}
