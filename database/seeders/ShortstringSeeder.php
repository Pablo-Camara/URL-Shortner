<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortstringSeeder extends Seeder
{

    private $totalInsertions = 0;

    private $maxShortstringsToInsertRecursively = null;

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
        if (
            !is_null($this->maxShortstringsToInsertRecursively)
            &&
            $this->totalInsertions >= $this->maxShortstringsToInsertRecursively
        ) {
            die('done! Inserted ' . $this->totalInsertions . ' rows');
        }
        if ($k == 0)
        {
            echo 'Trying to insert: ' . $prefix . PHP_EOL;
            try {
                DB::table('shortstrings')->insert([
                    'shortstring' => $prefix
                ]);
                echo 'Inserted: ' . $prefix . PHP_EOL;
                $this->totalInsertions++;
            } catch (\Throwable $th) {
                echo $th->getMessage() . PHP_EOL;
            }

            return;
        }

        for ($i = 0; $i < $n; ++$i)
        {
            $newPrefix = $prefix . $set[$i];
            $this->generateAllKLengthRec($set, $newPrefix, $n, $k - 1);
        }
    }




    private function seedUsingBaseConvert($totalLinksToSeed = null) {
        if ( is_null($totalLinksToSeed) ) {
            $totalLinksToSeed = env('TOTAL_SHORTSTRINGS_TO_SEED', $this->maxShortstringsToInsertRecursively);
        }
        for ($i = 0; $i < $totalLinksToSeed; $i++){
            $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $totalAlphabetChars = strlen($alphabet);
            $shortstring = base_convert($i, 10, $totalAlphabetChars);

            echo 'Trying to insert: ' . $shortstring . PHP_EOL;
            try {
                DB::table('shortstrings')->insert([
                    'shortstring' => $shortstring
                ]);
                echo 'Inserted: ' . $shortstring . PHP_EOL;
                $this->totalInsertions++;
            } catch (\Throwable $th) {
                echo $th->getMessage() . PHP_EOL;
            }

        }
        echo PHP_EOL . 'Total insertions: ' . $this->totalInsertions . PHP_EOL;
    }

    private function seedAllPossibilitiesUsingRecursiveFunction($totalShortstringsToInsert = null, $stringLengthToStartFrom = null) {

        if ( is_null($totalShortstringsToInsert) ) {
            $totalShortstringsToInsert = env('TOTAL_SHORTSTRINGS_TO_SEED', $this->maxShortstringsToInsertRecursively);
        };


        $this->maxShortstringsToInsertRecursively = $totalShortstringsToInsert;

        if ( is_null($stringLengthToStartFrom) ) {
            $stringLengthToStartFrom = env('SHORTSTRINGS_LENGTH', 3);
        };

        $strLen = $stringLengthToStartFrom;

        $alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $set = array_merge($numbers, $alphabet);

        while (true) {
            $this->generateAllKLength($set, $strLen);
            $strLen++;
        }
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // We could either seed using base_convert
        // which has less combinations per string length starting on string length 2
        // because it does not consider zeros on the left for the combinations.
        // or we could seed using a recursive function that consider combinations
        // with zeros on the left.

        // we can use the following env variable
        // when calling the seeder from CLI
        // to decide wheter to use 'base_convert' or 'recursive' functions
        // default is recursive
        $seedMethod = env('SHORTSTRING_SEED_METHOD', 'recursive');

        if (!in_array(strtolower($seedMethod), ['recursive', 'base_convert'])) {
            $seedMethod = 'recursive';
        }

        echo 'Will use the "' . $seedMethod . '" method to seed the shortstrings.' . PHP_EOL;

        // for both function we can either pass parameters here directly
        // in the seeder function params
        // or when calling the seeder we can set ENV variables
        // these ENV variables have defaults already set for us:

        // TOTAL_SHORTSTRINGS_TO_SEED = 100000 ( set in the private variable
        // on top of this file/class $maxShortstringsToInsertRecursively


        if ($seedMethod === 'base_convert') {
            $this->seedUsingBaseConvert();
        }


        // and for the below function we have the env var
        // called SHORTSTRINGS_LENGTH = 3 ( default )
        // which define where to start in terms of string length
        // to generate all possibilities starting from that string length

        if ($seedMethod === 'recursive') {
            $this->seedAllPossibilitiesUsingRecursiveFunction();
        }

    }
}
