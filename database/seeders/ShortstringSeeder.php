<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortstringSeeder extends Seeder
{

    private function generateAllKLength($set, $k)
    {
        $n = count($set);
        $this->generateAllKLengthRec($set, "", $n, $k);
    }


    private function generateAllKLengthRec(
        array $set,
        string $prefix,
        int $n,
        int $k
    )
    {
        if ($k == 0)
        {
            echo 'Inserting: ' . $prefix . PHP_EOL;
            DB::table('shortstrings')->insert([
                'shortstring' => $prefix
            ]);
            return;
        }

        for ($i = 0; $i < $n; ++$i)
        {
            $newPrefix = $prefix . $set[$i];
            $this->generateAllKLengthRec($set, $newPrefix, $n, $k - 1);
        }
    }




    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $set = array_merge($alphabet, $numbers);

        for($k = 3; $k <= 10; $k++){
            $this->generateAllKLength($set, $k);
        }

    }
}
