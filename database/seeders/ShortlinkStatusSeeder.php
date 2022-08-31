<?php

namespace Database\Seeders;

use App\Models\Shortlink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortlinkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shortlinkStatuses = [
            [
                'id' => Shortlink::STATUS_ACTIVE,
                'name' => 'ACTIVE'
            ],
            [
                'id' => Shortlink::STATUS_DELETED,
                'name' => 'DELETED'
            ],
            [
                'id' => Shortlink::STATUS_SUSPENDED,
                'name' => 'SUSPENDED'
            ]
        ];

        foreach($shortlinkStatuses as $shortlinkStatus) {
            DB::table('shortlink_statuses')->insert($shortlinkStatus);
        }

    }
}
