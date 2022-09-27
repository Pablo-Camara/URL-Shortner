<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\AuthResponses;
use App\Models\PermissionGroup;
use App\Models\User;
use Carbon\Carbon;
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
            DB::raw('permission_groups.name AS `'.__('admin-panel.permission-group').'`'),
            DB::raw('CASE WHEN users.email_verified_at IS NOT NULL THEN 1 ELSE 0 END AS `'.__('admin-panel.has-verified-email').'`'),
            DB::raw('CASE WHEN users.password IS NOT NULL THEN 1 ELSE 0 END AS `'.__('admin-panel.has-password').'`'),
            DB::raw('users.created_at AS `'.__('admin-panel.created_at').'`'),
            DB::raw('users.updated_at AS `'.__('admin-panel.updated_at').'`'),
        ])->leftJoin(
            'permission_groups', 'permission_groups.id', '=', 'users.permission_group_id'
        )->where('guest', '=', 0);

        $freeTextSearchValue = trim($request->input('free-search', ''));

        if (!empty($freeTextSearchValue)) {
            $results = $results->where(function($query) use ($freeTextSearchValue) {
                $query->where(
                    'users.email', 'LIKE', '%'.$freeTextSearchValue.'%'
                )->orWhere(
                    'users.name', 'LIKE', '%'.$freeTextSearchValue.'%'
                )->orWhere(
                    'permission_groups.name' , 'LIKE', '%'.$freeTextSearchValue.'%'
                )->orWhere(
                    'users.id', '=', $freeTextSearchValue
                );
            });
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

        return new Response(
            [
                'search_results' => $results,
                'pagination_identifier' => 'UsersList',
                'edit_config' => [
                    'primary_key_column' =>  __('admin-panel.user-id')
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

        $user = User::findOrFail($request->input('id'));

        return [
            'form_title' => 'Editar utilizador',
            'save_endpoint' => url('/api/users/edit'),
            'form_fields' => [
                [
                    'label' => __('admin-panel.user-id'),
                    'element_type' => 'input',
                    'element_attributes' => [
                        'name' => 'id',
                        'type' => 'text',
                        'disabled' => 'disabled',
                        'value' => $user->id,
                    ]
                ],
                [
                    'label' => __('admin-panel.name'),
                    'element_type' => 'input',
                    'element_attributes' => [
                        'name' => 'name',
                        'type' => 'text',
                        'disabled' => 'disabled',
                        'value' => $user->name,
                    ],
                ],
                [
                    'label' => __('admin-panel.permission-group'),
                    'element_type' => 'select',
                    'element_attributes' => [
                        'name' => 'permission_group_id',
                        'value' => $user->permission_group_id,
                    ],
                    'options' => PermissionGroup::all([
                        DB::raw('id AS value'),
                        DB::raw('name AS text')
                    ]),
                ]
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
            'permission_group_id' => 'required|numeric',
        ];

        $request->validate($validations);

        $user = User::findOrFail($request->input('id'));
        PermissionGroup::findOrFail($request->input('permission_group_id'));

        $user->permission_group_id = $request->input('permission_group_id');
        $user->save();

        return new Response([
            'message' => 'Guardado! ( ' . (Carbon::now()->toDateTimeString()) . ' )'
        ], 200);
    }
}
