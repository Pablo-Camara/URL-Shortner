<?php

namespace Database\Seeders;

use App\Helpers\Actions\AuthActions;
use App\Helpers\Actions\HomeControllerActions;
use App\Helpers\Actions\MenuActions;
use App\Helpers\Actions\ShortlinkActions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

class ActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authActions = (new ReflectionClass(AuthActions::class))->getConstants();
        $shortlinkActions = (new ReflectionClass(ShortlinkActions::class))->getConstants();
        $menuActions = (new ReflectionClass(MenuActions::class))->getConstants();
        $homeControllerActions = (new ReflectionClass(HomeControllerActions::class))->getConstants();


        $actions = array_map(
            function ($action) {
                return ['name' => $action];
            },
            array_merge($authActions, $shortlinkActions, $menuActions, $homeControllerActions)
        );

        foreach($actions as $action) {
            DB::table('actions')->insert($action);
        }
    }
}
