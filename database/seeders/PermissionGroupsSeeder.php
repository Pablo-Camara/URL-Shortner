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
                'name' => 'utilizador básico',
                'default' => 1,
                'edit_shortlinks_destination_url' => 1,
                'view_shortlinks_total_views' => 1,
                'view_shortlinks_total_unique_views' => 0,
            ],
            [
                'name' => 'wil-admin',
                'default' => 0,
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
