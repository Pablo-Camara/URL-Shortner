<?php

namespace Database\Seeders;

use App\Helpers\Auth\Abilities\AdminAbilities;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminAbilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminAbilities = [
            [
                'name' => AdminAbilities::ADMIN
            ]
        ];

        foreach($adminAbilities as $adminAbility) {
            DB::table('abilities')->insert($adminAbility);
        }

    }
}
