<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortstringSeeder extends Seeder
{
    private $tempTable = 'shortstrings_temp';
    private $liveTable = 'shortstrings';
    private $insertCount = 0;
    private $skipUntilInsertNumber = null;

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
            $this->insertCount++;
            if (!is_null($this->skipUntilInsertNumber) && $this->insertCount < $this->skipUntilInsertNumber) {
                return;
            }

            echo 'Inserting: ' . $prefix . PHP_EOL;
            DB::table($this->tempTable)->insert([
                'shortstring' => $prefix,
                'length' => strlen($prefix)
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


        $stringLength = env('SHORTSTRINGS_LENGTH', null);
        $this->skipUntilInsertNumber = env('SKIP_UNTIL_INSERT_NUMBER', null);
        $totalCombinationsPossible = (count($set)**$stringLength);
        if (empty($stringLength)) {
            echo PHP_EOL . 'Must set SHORTSTRINGS_LENGTH env variable when running the seeder.' . PHP_EOL;
            echo 'Example, seed all combinations with 1 of length:' . PHP_EOL;
            echo 'SHORTSTRINGS_LENGTH=1 php artisan db:seed --class=ShortstringSeeder' . PHP_EOL . PHP_EOL;
            die();
        }

        if (env('SKIP_CREATE_TEMP_TABLE', null) === null) {
            echo PHP_EOL . PHP_EOL . 'Will create temporary table: ' . $this->tempTable . PHP_EOL . PHP_EOL;
            $tempTableCreate = DB::statement("CREATE TABLE " . $this->tempTable . ' LIKE ' . $this->liveTable);
        }


        echo PHP_EOL . PHP_EOL . 'Will generate all shortstring combinations with the length of ' . $stringLength . '.' . PHP_EOL . PHP_EOL;
        echo PHP_EOL . PHP_EOL . 'Alphabet that will be used: ' . implode(',', $set) . PHP_EOL . PHP_EOL;
        echo PHP_EOL . PHP_EOL . 'Total combinations possible: ' . $totalCombinationsPossible . PHP_EOL . PHP_EOL;
        $this->generateAllKLength($set, $stringLength);

        echo PHP_EOL . PHP_EOL . 'Done generating all the combinations possible.  (' . $totalCombinationsPossible . ')' . PHP_EOL . PHP_EOL;
        echo PHP_EOL . PHP_EOL . 'Will now insert the ' . $totalCombinationsPossible . ' combinations in a random order into the live table: ' . $this->liveTable . PHP_EOL . PHP_EOL;

        $copyDataSql = "INSERT INTO " . $this->liveTable . " (shortstring, is_available, is_custom, length)
        SELECT shortstring, is_available, is_custom, length
        FROM " . $this->tempTable . "
        ORDER BY RAND();";

        $insertedRows = DB::affectingStatement($copyDataSql);

        echo PHP_EOL . PHP_EOL . 'Inserted ' . $insertedRows . ' new shortstrings with length of ' . $stringLength . '.' . PHP_EOL . PHP_EOL;

        if (env('SKIP_CREATE_TEMP_TABLE', null) === null) {
            echo PHP_EOL . PHP_EOL . 'Will now drop the temporary table: ' . $this->tempTable . PHP_EOL . PHP_EOL;
            $dropTempTable = DB::statement( "DROP TABLE " . $this->tempTable);
        }


    }
}
