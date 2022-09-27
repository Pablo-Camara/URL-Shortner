<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionGroups = [
            [
                'name' => 'convidado',
                'guests_permission_group' => 1,
                'send_shortlink_by_email_when_generating' => 0,
                'edit_shortlinks_destination_url' => 0,
                'view_shortlinks_total_views' => 0,
                'view_shortlinks_total_unique_views' => 0,
            ],
            [
                'name' => 'utilizador registrado',
                'default' => 1,
                'send_shortlink_by_email_when_generating' => 1,
                'edit_shortlinks_destination_url' => 1,
                'view_shortlinks_total_views' => 1,
                'view_shortlinks_total_unique_views' => 0,
            ],
            [
                'name' => 'wil-admin',
                'send_shortlink_by_email_when_generating' => 1,
                'edit_shortlinks_destination_url' => 1,
                'view_shortlinks_total_views' => 1,
                'view_shortlinks_total_unique_views' => 1,
            ]
        ];

        foreach($permissionGroups as $permissionGroup) {
            DB::table('permission_groups')->insert($permissionGroup);
        }

    }
}
