<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\AuthResponses;
use App\Models\PermissionGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PermissionGroupController extends Controller
{

    private $paginationIdentifier = 'PermissionGroupsList';

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

        $transformNullToUndefined = function ($nullOrNumber) {
            return is_null($nullOrNumber) ? __('admin-panel.undefined') : $nullOrNumber;
        };

        $colsToTransformValues = [
            __('admin-panel.default-permission-group') => $transformBinaryToText,
            __('admin-panel.guests_permission_group') => $transformBinaryToText,
            __('admin-panel.send_shortlink_by_email_when_generating') => $transformBinaryToText,
            __('admin-panel.edit_shortlinks_destination_url') => $transformBinaryToText,
            __('admin-panel.view_shortlinks_total_views') => $transformBinaryToText,
            __('admin-panel.view_shortlinks_total_unique_views') => $transformBinaryToText,
            __('admin-panel.create_custom_shortlinks') => $transformBinaryToText,

            __('admin-panel.max_shortlinks_with_5_or_more_of_length') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_day_with_5_or_more_of_length') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_month_with_5_or_more_of_length') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_year_with_5_or_more_of_length') => $transformNullToUndefined,

            __('admin-panel.create_shortlinks_with_length_4') => $transformBinaryToText,
            __('admin-panel.max_shortlinks_with_length_4') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_day_with_length_4') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_month_with_length_4') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_year_with_length_4') => $transformNullToUndefined,

            __('admin-panel.create_shortlinks_with_length_3') => $transformBinaryToText,
            __('admin-panel.max_shortlinks_with_length_3') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_day_with_length_3') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_month_with_length_3') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_year_with_length_3') => $transformNullToUndefined,

            __('admin-panel.create_shortlinks_with_length_2') => $transformBinaryToText,
            __('admin-panel.max_shortlinks_with_length_2') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_day_with_length_2') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_month_with_length_2') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_year_with_length_2') => $transformNullToUndefined,

            __('admin-panel.create_shortlinks_with_length_1') => $transformBinaryToText,
            __('admin-panel.max_shortlinks_with_length_1') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_day_with_length_1') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_month_with_length_1') => $transformNullToUndefined,
            __('admin-panel.max_shortlinks_per_year_with_length_1') => $transformNullToUndefined,
        ];



        $results = PermissionGroup::select([
            DB::raw('permission_groups.id AS `'.__('admin-panel.permission-group-id').'`'),
            DB::raw('permission_groups.name AS `'.__('admin-panel.name').'`'),
            DB::raw('permission_groups.default AS `'.__('admin-panel.default-permission-group').'`'),
            DB::raw('permission_groups.guests_permission_group AS `'.__('admin-panel.guests_permission_group').'`'),
            DB::raw('permission_groups.send_shortlink_by_email_when_generating AS `'.__('admin-panel.send_shortlink_by_email_when_generating').'`'),
            DB::raw('permission_groups.edit_shortlinks_destination_url AS `'.__('admin-panel.edit_shortlinks_destination_url').'`'),
            DB::raw('permission_groups.view_shortlinks_total_views AS `'.__('admin-panel.view_shortlinks_total_views').'`'),
            DB::raw('permission_groups.view_shortlinks_total_unique_views AS `'.__('admin-panel.view_shortlinks_total_unique_views').'`'),
            DB::raw('permission_groups.create_custom_shortlinks AS `'.__('admin-panel.create_custom_shortlinks').'`'),

            DB::raw('permission_groups.max_shortlinks_with_5_or_more_of_length AS `'.__('admin-panel.max_shortlinks_with_5_or_more_of_length').'`'),
            DB::raw('permission_groups.max_shortlinks_per_day_with_5_or_more_of_length AS `'.__('admin-panel.max_shortlinks_per_day_with_5_or_more_of_length').'`'),
            DB::raw('permission_groups.max_shortlinks_per_month_with_5_or_more_of_length AS `'.__('admin-panel.max_shortlinks_per_month_with_5_or_more_of_length').'`'),
            DB::raw('permission_groups.max_shortlinks_per_year_with_5_or_more_of_length AS `'.__('admin-panel.max_shortlinks_per_year_with_5_or_more_of_length').'`'),

            DB::raw('permission_groups.create_shortlinks_with_length_4 AS `'.__('admin-panel.create_shortlinks_with_length_4').'`'),
            DB::raw('permission_groups.max_shortlinks_with_length_4 AS `'.__('admin-panel.max_shortlinks_with_length_4').'`'),
            DB::raw('permission_groups.max_shortlinks_per_day_with_length_4 AS `'.__('admin-panel.max_shortlinks_per_day_with_length_4').'`'),
            DB::raw('permission_groups.max_shortlinks_per_month_with_length_4 AS `'.__('admin-panel.max_shortlinks_per_month_with_length_4').'`'),
            DB::raw('permission_groups.max_shortlinks_per_year_with_length_4 AS `'.__('admin-panel.max_shortlinks_per_year_with_length_4').'`'),

            DB::raw('permission_groups.create_shortlinks_with_length_3 AS `'.__('admin-panel.create_shortlinks_with_length_3').'`'),
            DB::raw('permission_groups.max_shortlinks_with_length_3 AS `'.__('admin-panel.max_shortlinks_with_length_3').'`'),
            DB::raw('permission_groups.max_shortlinks_per_day_with_length_3 AS `'.__('admin-panel.max_shortlinks_per_day_with_length_3').'`'),
            DB::raw('permission_groups.max_shortlinks_per_month_with_length_3 AS `'.__('admin-panel.max_shortlinks_per_month_with_length_3').'`'),
            DB::raw('permission_groups.max_shortlinks_per_year_with_length_3 AS `'.__('admin-panel.max_shortlinks_per_year_with_length_3').'`'),

            DB::raw('permission_groups.create_shortlinks_with_length_2 AS `'.__('admin-panel.create_shortlinks_with_length_2').'`'),
            DB::raw('permission_groups.max_shortlinks_with_length_2 AS `'.__('admin-panel.max_shortlinks_with_length_2').'`'),
            DB::raw('permission_groups.max_shortlinks_per_day_with_length_2 AS `'.__('admin-panel.max_shortlinks_per_day_with_length_2').'`'),
            DB::raw('permission_groups.max_shortlinks_per_month_with_length_2 AS `'.__('admin-panel.max_shortlinks_per_month_with_length_2').'`'),
            DB::raw('permission_groups.max_shortlinks_per_year_with_length_2 AS `'.__('admin-panel.max_shortlinks_per_year_with_length_2').'`'),

            DB::raw('permission_groups.create_shortlinks_with_length_1 AS `'.__('admin-panel.create_shortlinks_with_length_1').'`'),
            DB::raw('permission_groups.max_shortlinks_with_length_1 AS `'.__('admin-panel.max_shortlinks_with_length_1').'`'),
            DB::raw('permission_groups.max_shortlinks_per_day_with_length_1 AS `'.__('admin-panel.max_shortlinks_per_day_with_length_1').'`'),
            DB::raw('permission_groups.max_shortlinks_per_month_with_length_1 AS `'.__('admin-panel.max_shortlinks_per_month_with_length_1').'`'),
            DB::raw('permission_groups.max_shortlinks_per_year_with_length_1 AS `'.__('admin-panel.max_shortlinks_per_year_with_length_1').'`'),

            DB::raw('permission_groups.created_at AS `'.__('admin-panel.created_at').'`'),
            DB::raw('permission_groups.updated_at AS `'.__('admin-panel.updated_at').'`'),
        ]);


        $results = $results->paginate(100)->toArray();

        $results['data'] = array_map(
            function($row) use ($colsToTranslateValues, $colsToTransformValues) {
                foreach($colsToTranslateValues as $colName) {
                    if (!array_key_exists($colName, $row)) {
                        continue;
                    }

                    $row[$colName] = __('admin-panel.' . $row[$colName]);
                }

                foreach($colsToTransformValues as $colName => $transformFunction) {
                    if (!array_key_exists($colName, $row)) {
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
                'pagination_identifier' => $this->paginationIdentifier,
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
        $permissionGroup = PermissionGroup::find($request->input('id'));

        $edit = false;
        if ($permissionGroup) {
            $edit = true;
        } else {
            // will create instance but not save
            // will only save in the save/edit endpoint
            $permissionGroup = new PermissionGroup();
        }

        $sendShortlinksByEmailWhenGenerating = [
            'name' => 'send_shortlink_by_email_when_generating',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canSendShortlinkByEmailWhenGenerating() || $edit == false) {
            $sendShortlinksByEmailWhenGenerating['checked'] = 'checked';
        }

        $editShortlinksDestinationUrlAttributes = [
            'name' => 'edit_shortlinks_destination_url',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canEditShortlinksDestinationUrl() || $edit == false) {
            $editShortlinksDestinationUrlAttributes['checked'] = 'checked';
        }

        $canViewShortlinksTotalViews = [
            'name' => 'view_shortlinks_total_views',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canViewShortlinksTotalViews() || $edit == false) {
            $canViewShortlinksTotalViews['checked'] = 'checked';
        }

        $viewShortlinksTotalUniqueViews = [
            'name' => 'view_shortlinks_total_unique_views',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canViewShortlinksTotalUniqueViews()) {
            $viewShortlinksTotalUniqueViews['checked'] = 'checked';
        }

        $createCustomShortlinks = [
            'name' => 'create_custom_shortlinks',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canCreateCustomShortlinks()) {
            $createCustomShortlinks['checked'] = 'checked';
        }


        $createShortlinksWithLength1 = [
            'name' => 'create_shortlinks_with_length_1',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canCreateShortlinksWithSpecificLength(1)) {
            $createShortlinksWithLength1['checked'] = 'checked';
        }

        $createShortlinksWithLength2 = [
            'name' => 'create_shortlinks_with_length_2',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canCreateShortlinksWithSpecificLength(2)) {
            $createShortlinksWithLength2['checked'] = 'checked';
        }

        $createShortlinksWithLength3 = [
            'name' => 'create_shortlinks_with_length_3',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canCreateShortlinksWithSpecificLength(3)) {
            $createShortlinksWithLength3['checked'] = 'checked';
        }

        $createShortlinksWithLength4 = [
            'name' => 'create_shortlinks_with_length_4',
            'type' => 'checkbox'
        ];
        if ($permissionGroup->canCreateShortlinksWithSpecificLength(4)) {
            $createShortlinksWithLength4['checked'] = 'checked';
        }

        $formFields = [
            [
                'label' => __('admin-panel.name'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'name',
                    'type' => 'text',
                    'value' => $edit ? $permissionGroup->name : '',
                ],
            ],
            [
                'label' => __('admin-panel.send_shortlink_by_email_when_generating'),
                'element_type' => 'input',
                'element_attributes' => $sendShortlinksByEmailWhenGenerating,
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
            [
                'label' => __('admin-panel.create_custom_shortlinks'),
                'element_type' => 'input',
                'element_attributes' => $createCustomShortlinks,
            ],
            [
                'label' => __('admin-panel.max_shortlinks_with_5_or_more_of_length'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_with_5_or_more_of_length',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_with_5_or_more_of_length,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_day_with_5_or_more_of_length'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_day_with_5_or_more_of_length',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_day_with_5_or_more_of_length,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_month_with_5_or_more_of_length'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_month_with_5_or_more_of_length',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_month_with_5_or_more_of_length,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_year_with_5_or_more_of_length'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_year_with_5_or_more_of_length',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_year_with_5_or_more_of_length,
                ],
            ],
            [
                'label' => __('admin-panel.create_shortlinks_with_length_4'),
                'element_type' => 'input',
                'element_attributes' => $createShortlinksWithLength4,
            ],
            [
                'label' => __('admin-panel.max_shortlinks_with_length_4'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_with_length_4',
                    'type' => 'number',
                    'value' => $edit ? $permissionGroup->max_shortlinks_with_length_4 : 0,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_day_with_length_4'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_day_with_length_4',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_day_with_length_4,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_month_with_length_4'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_month_with_length_4',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_month_with_length_4,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_year_with_length_4'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_year_with_length_4',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_year_with_length_4,
                ],
            ],
            [
                'label' => __('admin-panel.create_shortlinks_with_length_3'),
                'element_type' => 'input',
                'element_attributes' => $createShortlinksWithLength3,
            ],
            [
                'label' => __('admin-panel.max_shortlinks_with_length_3'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_with_length_3',
                    'type' => 'number',
                    'value' => $edit ? $permissionGroup->max_shortlinks_with_length_3 : 0,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_day_with_length_3'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_day_with_length_3',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_day_with_length_3,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_month_with_length_3'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_month_with_length_3',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_month_with_length_3,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_year_with_length_3'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_year_with_length_3',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_year_with_length_3,
                ],
            ],
            [
                'label' => __('admin-panel.create_shortlinks_with_length_2'),
                'element_type' => 'input',
                'element_attributes' => $createShortlinksWithLength2,
            ],
            [
                'label' => __('admin-panel.max_shortlinks_with_length_2'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_with_length_2',
                    'type' => 'number',
                    'value' => $edit ? $permissionGroup->max_shortlinks_with_length_2 : 0,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_day_with_length_2'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_day_with_length_2',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_day_with_length_2,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_month_with_length_2'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_month_with_length_2',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_month_with_length_2,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_year_with_length_2'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_year_with_length_2',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_year_with_length_2,
                ],
            ],
            [
                'label' => __('admin-panel.create_shortlinks_with_length_1'),
                'element_type' => 'input',
                'element_attributes' => $createShortlinksWithLength1,
            ],
            [
                'label' => __('admin-panel.max_shortlinks_with_length_1'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_with_length_1',
                    'type' => 'number',
                    'value' => $edit ? $permissionGroup->max_shortlinks_with_length_1 : 0,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_day_with_length_1'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_day_with_length_1',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_day_with_length_1,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_month_with_length_1'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_month_with_length_1',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_month_with_length_1,
                ],
            ],
            [
                'label' => __('admin-panel.max_shortlinks_per_year_with_length_1'),
                'element_type' => 'input',
                'element_attributes' => [
                    'name' => 'max_shortlinks_per_year_with_length_1',
                    'type' => 'number',
                    'value' => $permissionGroup->max_shortlinks_per_year_with_length_1,
                ],
            ],
        ];

        if ($edit) {
            array_unshift(
                $formFields,
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
            );
        }

        $configurationsArr = [
            'form_title' => ($edit ? 'Editar' : 'Criar') . ' grupo de permissões',
            'save_endpoint' => url('/api/permission-groups/edit'),
            'form_fields' => $formFields
        ];

        if (
            $edit
            &&
            // removing default permission groups is not allowed.
            !$permissionGroup->isDefaultForGuestUsers()
            &&
            !$permissionGroup->isDefaultForNewRegisteredUsers()
        ) {
            $totalUsersWithThisPermissionGroup = User::where(
                'permission_group_id', '=', $permissionGroup->id
            )->count();

            // "smart" text :D - plural vs singular
            $removeButtonConfirmationText = 'Este grupo está associado a <b>'.$totalUsersWithThisPermissionGroup.'</b> ' . ($totalUsersWithThisPermissionGroup === 1 ? 'utilizador que será movido' : 'utilizadores que serão movidos') . ' para o grupo de permissões predefinido para novos utilizadores, tem a certeza que deseja continuar?';

            $configurationsArr = array_merge(
                $configurationsArr,
                [
                    'remove_endpoint' => url('/api/permission-groups/remove'),
                    'remove_button_text' => 'Quero remover este grupo de permissões',
                    'remove_button_confirmation_text' => $removeButtonConfirmationText,
                    'entity_id' => $permissionGroup->id
                ]
            );
        }

        return $configurationsArr;
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
            'id' => 'numeric',
            'name' => 'required',
        ];

        Validator::make(
            $request->all(),
            $validations,
            ['name.required' => 'É necessário dar um nome ao grupo de permissões.'],
            []
        )->validate();

        $permissionGroupName = trim($request->input('name'));

        if (!empty($request->input('id', null))) {
            $permissionGroup = PermissionGroup::findOrFail($request->input('id'));
        } else {
            $permissionGroup = new PermissionGroup();
            $permissionGroupWithEqualName = PermissionGroup::where('name', '=', $permissionGroupName)->first();

            if ($permissionGroupWithEqualName) {
                throw ValidationException::withMessages([
                    'name' => 'Já existe um grupo de permissões com este mesmo nome.'
                ]);
            }
        }

        $permissionGroup->name = $permissionGroupName;
        $permissionGroup->send_shortlink_by_email_when_generating = !empty($request->input('send_shortlink_by_email_when_generating')) ? 1 : 0;
        $permissionGroup->edit_shortlinks_destination_url = !empty($request->input('edit_shortlinks_destination_url')) ? 1 : 0;
        $permissionGroup->view_shortlinks_total_views = !empty($request->input('view_shortlinks_total_views')) ? 1 : 0;
        $permissionGroup->view_shortlinks_total_unique_views = !empty($request->input('view_shortlinks_total_unique_views')) ? 1 : 0;
        $permissionGroup->create_custom_shortlinks = !empty($request->input('create_custom_shortlinks')) ? 1 : 0;

        $maxShortlinksWith5orMoreOfLength = $request->input('max_shortlinks_with_5_or_more_of_length');
        if (is_numeric($maxShortlinksWith5orMoreOfLength)) {
            $permissionGroup->max_shortlinks_with_5_or_more_of_length = $maxShortlinksWith5orMoreOfLength;
        } else {
            $permissionGroup->max_shortlinks_with_5_or_more_of_length = null;
        }

        $maxShortlinksWith5orMoreOfLengthPerDay = $request->input('max_shortlinks_per_day_with_5_or_more_of_length');
        if (is_numeric($maxShortlinksWith5orMoreOfLengthPerDay)) {
            $permissionGroup->max_shortlinks_per_day_with_5_or_more_of_length = $maxShortlinksWith5orMoreOfLengthPerDay;
        } else {
            $permissionGroup->max_shortlinks_per_day_with_5_or_more_of_length = null;
        }

        $maxShortlinksWith5orMoreOfLengthPerMonth = $request->input('max_shortlinks_per_month_with_5_or_more_of_length');
        if (is_numeric($maxShortlinksWith5orMoreOfLengthPerMonth)) {
            $permissionGroup->max_shortlinks_per_month_with_5_or_more_of_length = $maxShortlinksWith5orMoreOfLengthPerMonth;
        } else {
            $permissionGroup->max_shortlinks_per_month_with_5_or_more_of_length = null;
        }

        $maxShortlinksWith5orMoreOfLengthPerYear = $request->input('max_shortlinks_per_year_with_5_or_more_of_length');
        if (is_numeric($maxShortlinksWith5orMoreOfLengthPerYear)) {
            $permissionGroup->max_shortlinks_per_year_with_5_or_more_of_length = $maxShortlinksWith5orMoreOfLengthPerYear;
        } else {
            $permissionGroup->max_shortlinks_per_year_with_5_or_more_of_length = null;
        }

        if (
            is_numeric($maxShortlinksWith5orMoreOfLength)
            &&
            (int)$maxShortlinksWith5orMoreOfLength > 0
        ) {
            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerDay)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerDay === 0
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_day_with_5_or_more_of_length' => 'Definiste um limite total de ' . $maxShortlinksWith5orMoreOfLength . ' shortlinks ( para shortlinks com 5 ou mais de tamanho ), mas depois também definiste um total diário = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                ]);
            }

            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerDay)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerDay > (int)$maxShortlinksWith5orMoreOfLength
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_day_with_5_or_more_of_length' => 'O limite diário de shortlinks não pode ser superior ao limite total definido.'
                ]);
            }

            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerMonth)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerMonth === 0
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_month_with_5_or_more_of_length' => 'Definiste um limite total de ' . $maxShortlinksWith5orMoreOfLength . ' shortlinks ( para shortlinks com 5 ou mais de tamanho ), mas depois também definiste um total mensal = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                ]);
            }

            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerDay)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerMonth > (int)$maxShortlinksWith5orMoreOfLength
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_month_with_5_or_more_of_length' => 'O limite mensal de shortlinks não pode ser superior ao limite total definido.'
                ]);
            }


            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerYear)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerYear === 0
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_year_with_5_or_more_of_length' => 'Definiste um limite total de ' . $maxShortlinksWith5orMoreOfLength . ' shortlinks ( para shortlinks com 5 ou mais de tamanho ), mas depois também definiste um total anual = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                ]);
            }

            if (
                is_numeric($maxShortlinksWith5orMoreOfLengthPerDay)
                &&
                (int)$maxShortlinksWith5orMoreOfLengthPerYear > (int)$maxShortlinksWith5orMoreOfLength
            ) {
                throw ValidationException::withMessages([
                    'max_shortlinks_per_year_with_5_or_more_of_length' => 'O limite anual de shortlinks não pode ser superior ao limite total definido.'
                ]);
            }
        }

        for($i = 4; $i >= 1; $i--) {
            $createPermission = 'create_shortlinks_with_length_' . $i;

            $permissionGroup->$createPermission = !empty($request->input($createPermission)) ? 1 : 0;

            $totalVar = 'max_shortlinks_with_length_' . $i;
            if (is_numeric($request->input($totalVar))) {
                $permissionGroup->$totalVar = $request->input($totalVar);
            } else {
                $permissionGroup->$totalVar = null;
            }

            $perDayVar = 'max_shortlinks_per_day_with_length_' . $i;
            if (is_numeric($request->input($perDayVar))) {
                $permissionGroup->$perDayVar = $request->input($perDayVar);
            } else {
                $permissionGroup->$perDayVar = null;
            }

            $perMonthVar = 'max_shortlinks_per_month_with_length_' . $i;
            if (is_numeric($request->input($perMonthVar))) {
                $permissionGroup->$perMonthVar = $request->input($perMonthVar);
            } else {
                $permissionGroup->$perMonthVar = null;
            }

            $perYearVar = 'max_shortlinks_per_year_with_length_' . $i;
            if (is_numeric($request->input($perYearVar))) {
                $permissionGroup->$perYearVar = $request->input($perYearVar);
            } else {
                $permissionGroup->$perYearVar = null;
            }


            if (
                is_numeric($request->input($totalVar))
                &&
                (int)$request->input($totalVar) > 0
            ) {
                if (
                    is_numeric($request->input($perDayVar))
                    &&
                    (int)$request->input($perDayVar) === 0
                ) {
                    throw ValidationException::withMessages([
                        $perDayVar => 'Definiste um limite total de ' . $request->input($totalVar) . ' shortlinks ( para shortlinks com '.$i.' de tamanho ), mas depois também definiste um total diário = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                    ]);
                }

                if (
                    is_numeric($request->input($perDayVar))
                    &&
                    (int)$request->input($perDayVar) > (int)$request->input($totalVar)
                ) {
                    throw ValidationException::withMessages([
                        $perDayVar => 'O limite diário de shortlinks não pode ser superior ao limite total definido.'
                    ]);
                }

                if (
                    is_numeric($request->input($perMonthVar))
                    &&
                    (int)$request->input($perMonthVar) === 0
                ) {
                    throw ValidationException::withMessages([
                        $perMonthVar => 'Definiste um limite total de ' . $request->input($totalVar) . ' shortlinks ( para shortlinks com '.$i.' de tamanho ), mas depois também definiste um total mensal = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                    ]);
                }

                if (
                    is_numeric($request->input($perMonthVar))
                    &&
                    (int)$request->input($perMonthVar) > (int)$request->input($totalVar)
                ) {
                    throw ValidationException::withMessages([
                        $perMonthVar => 'O limite mensal de shortlinks não pode ser superior ao limite total definido.'
                    ]);
                }


                if (
                    is_numeric($request->input($perYearVar))
                    &&
                    (int)$request->input($perYearVar) === 0
                ) {
                    throw ValidationException::withMessages([
                        $perYearVar => 'Definiste um limite total de ' . $request->input($totalVar) . ' shortlinks ( para shortlinks com '.$i.' de tamanho ), mas depois também definiste um total anual = 0, que não faz sentido pois impediria os utilizadores deste grupo de usufruirem do limite total que definiste.'
                    ]);
                }

                if (
                    is_numeric($request->input($perYearVar))
                    &&
                    (int)$request->input($perYearVar) > (int)$request->input($totalVar)
                ) {
                    throw ValidationException::withMessages([
                        $perYearVar => 'O limite anual de shortlinks não pode ser superior ao limite total definido.'
                    ]);
                }
            }
        }

        $permissionGroup->save();

        return new Response([
            'message' => 'Guardado! ( ' . (Carbon::now()->toDateTimeString()) . ' )',
            'pagination_identifier' => $this->paginationIdentifier,
        ], 200);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return AuthResponses::notAuthorized();
        }

        $validations = [
            'id' => 'required|numeric'
        ];

        Validator::make(
            $request->all(),
            $validations,
            ['id.required' => 'É enviar o ID do grupo do permissões no pedido de remoção.'],
            []
        )->validate();

        /**
         * @var PermissionGroup
         */
        $permissionGroupToRemove = PermissionGroup::find($request->input('id'));

        if (!$permissionGroupToRemove) {
            throw ValidationException::withMessages([
                'id' => 'Grupo de permissões não encontrado.'
            ]);
        }

        if (
            $permissionGroupToRemove->isDefaultForGuestUsers()
        ) {
            throw ValidationException::withMessages([
                'id' => 'Não é possível remover o grupo predefinido para utilizadores não registrados.'
            ]);
        }

        if (
            $permissionGroupToRemove->isDefaultForNewRegisteredUsers()
        ) {
            throw ValidationException::withMessages([
                'id' => 'Não é possível remover o grupo predefinido para novos utilizadores registrados.'
            ]);
        }

        $defaultPermissionGroupForNewRegisteredUsers = PermissionGroup::where(
            'default', '=', 1
        )->first();

        if (!$defaultPermissionGroupForNewRegisteredUsers) {
            throw ValidationException::withMessages([
                'id' => 'Grupo de permissões predefinido para novos utilizadores não encontrado. Impossível remover.'
            ]);
        }

        User::where(
            'permission_group_id', '=', $permissionGroupToRemove->id
        )->update([
            'permission_group_id' => $defaultPermissionGroupForNewRegisteredUsers->id
        ]);

        try {
            $permissionGroupToRemove->delete();
            return new Response('', 200);
        } catch (\Throwable $th) {
            throw $th;
        }


    }

}
