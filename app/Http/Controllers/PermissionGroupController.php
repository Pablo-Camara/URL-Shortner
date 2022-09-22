<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\AuthResponses;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PermissionGroupController extends Controller
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
            __('admin-panel.default-permission-group') => $transformBinaryToText,
            __('admin-panel.edit_shortlinks_destination_url') => $transformBinaryToText,
            __('admin-panel.view_shortlinks_total_views') => $transformBinaryToText,
            __('admin-panel.view_shortlinks_total_unique_views') => $transformBinaryToText,
        ];


        $results = PermissionGroup::select([
            DB::raw('permission_groups.id AS `'.__('admin-panel.permission-group-id').'`'),
            DB::raw('permission_groups.name AS `'.__('admin-panel.name').'`'),
            DB::raw('permission_groups.default AS `'.__('admin-panel.default-permission-group').'`'),
            DB::raw('permission_groups.edit_shortlinks_destination_url AS `'.__('admin-panel.edit_shortlinks_destination_url').'`'),
            DB::raw('permission_groups.view_shortlinks_total_views AS `'.__('admin-panel.view_shortlinks_total_views').'`'),
            DB::raw('permission_groups.view_shortlinks_total_unique_views AS `'.__('admin-panel.view_shortlinks_total_unique_views').'`'),
            DB::raw('permission_groups.created_at AS `'.__('admin-panel.created_at').'`'),
            DB::raw('permission_groups.updated_at AS `'.__('admin-panel.updated_at').'`'),
        ]);


        $results = $results->paginate(2)->toArray();

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
