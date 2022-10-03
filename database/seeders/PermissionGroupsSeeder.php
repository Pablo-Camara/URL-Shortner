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
                'guests_permission_group' => 1
            ],
            [
                'name' => 'utilizador registrado',
                'default' => 1,
                'send_shortlink_by_email_when_generating' => 1,
                'edit_shortlinks_destination_url' => 1,
                'view_shortlinks_total_views' => 1,
            ],
            [
                'name' => 'utilizador todo poderoso',
                'send_shortlink_by_email_when_generating' => 1,
                'edit_shortlinks_destination_url' => 1,
                'view_shortlinks_url_history' => 1,
                'view_shortlinks_total_views' => 1,
                'view_shortlinks_total_unique_views' => 1,
                'create_custom_shortlinks' => 1,
                'create_shortlinks_with_length_1' => 1,
                'max_shortlinks_with_length_1' => null,
                'create_shortlinks_with_length_2' => 1,
                'max_shortlinks_with_length_2' => null,
                'create_shortlinks_with_length_3' => 1,
                'max_shortlinks_with_length_3' => null,
                'create_shortlinks_with_length_4' => 1,
                'max_shortlinks_with_length_4' => null,
            ]
        ];

        foreach($permissionGroups as $permissionGroup) {
            DB::table('permission_groups')->insert($permissionGroup);
        }

    }
}
