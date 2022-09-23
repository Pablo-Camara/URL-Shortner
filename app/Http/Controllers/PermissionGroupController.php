<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\AuthResponses;
use App\Models\PermissionGroup;
use Carbon\Carbon;
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
                'search_results' => $results,
                'pagination_identifier' => 'PermissionGroupsList',
                'edit_config' => [
                    'primary_key_column' =>  __('admin-panel.permission-group-id')
                ]
            ]
        );

    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function prepareEditForm(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return AuthResponses::notAuthorized();
        }

        /**
         * @var PermissionGroup
         */
        $permissionGroup = PermissionGroup::findOrFail($request->input('id'));

        $editShortlinksDestinationUrlAttributes = [
            'name' => 'edit_shortlinks_destination_url',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canEditShortlinksDestinationUrl()) {
            $editShortlinksDestinationUrlAttributes['checked'] = 'checked';
        }

        $canViewShortlinksTotalViews = [
            'name' => 'view_shortlinks_total_views',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canViewShortlinksTotalViews()) {
            $canViewShortlinksTotalViews['checked'] = 'checked';
        }

        $viewShortlinksTotalUniqueViews = [
            'name' => 'view_shortlinks_total_unique_views',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canViewShortlinksTotalUniqueViews()) {
            $viewShortlinksTotalUniqueViews['checked'] = 'checked';
        }

        return [
            'form_title' => 'Editar grupo de permissões',
            'save_endpoint' => url('/api/permission-groups/edit'),
            'form_fields' => [
                [
                    'label' => __('admin-panel.permission-group-id'),
                    'element_type' => 'input',
                    'element_attributes' => [
                        'name' => 'id',
                        'type' => 'text',
                        'disabled' => 'disabled',
                        'value' => $permissionGroup->id,
                    ]
                ],
                [
                    'label' => __('admin-panel.name'),
                    'element_type' => 'input',
                    'element_attributes' => [
                        'name' => 'name',
                        'type' => 'text',
                        'value' => $permissionGroup->name,
                    ],
                ],
                [
                    'label' => __('admin-panel.edit_shortlinks_destination_url'),
                    'element_type' => 'input',
                    'element_attributes' => $editShortlinksDestinationUrlAttributes,
                ],
                [
                    'label' => __('admin-panel.view_shortlinks_total_views'),
                    'element_type' => 'input',
                    'element_attributes' => $canViewShortlinksTotalViews,
                ],
                [
                    'label' => __('admin-panel.view_shortlinks_total_unique_views'),
                    'element_type' => 'input',
                    'element_attributes' => $viewShortlinksTotalUniqueViews,
                ],
            ]
        ];
    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return AuthResponses::notAuthorized();
        }

        $validations = [
            'id' => 'required|numeric',
            'name' => 'required',
        ];

        $request->validate($validations);

        $permissionGroup = PermissionGroup::findOrFail($request->input('id'));

        $permissionGroup->name = $request->input('name');
        $permissionGroup->edit_shortlinks_destination_url = !empty($request->input('edit_shortlinks_destination_url')) ? 1 : 0;
        $permissionGroup->view_shortlinks_total_views = !empty($request->input('view_shortlinks_total_views')) ? 1 : 0;
        $permissionGroup->view_shortlinks_total_unique_views = !empty($request->input('view_shortlinks_total_unique_views')) ? 1 : 0;
        $permissionGroup->save();

        return new Response([
            'message' => 'Guardado! ( ' . (Carbon::now()->toDateTimeString()) . ' )'
        ], 200);
    }
}
