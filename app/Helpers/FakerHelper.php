<?php

namespace App\Helpers;

use Faker\Factory as Faker;


class FakerHelper
{
    /**
     * Generate random fake JSON data.
     *
     * @param int $minFields Minimum number of fields.
     * @param int $maxFields Maximum number of fields.
     * @return string
     */
    function generateJsonData(int $minFields = 5, int $maxFields = 10): string
    {
        $faker = Faker::create();
        $numFields = rand($minFields, $maxFields); // Random number of fields

        $fakeData = [];
        for ($i = 0; $i < $numFields; $i++) {
            $key = $faker->word;

            // Randomly decide the type of value (string, number, boolean, etc.)
            $valueType = rand(1, 4);
            switch ($valueType) {
                case 1:
                    $value = $faker->name; // String
                    break;
                case 2:
                    $value = $faker->numberBetween(1, 100); // Integer
                    break;
                case 3:
                    $value = $faker->boolean; // Boolean
                    break;
                case 4:
                    $value = $faker->date; // Date
                    break;
            }

            $fakeData[$key] = $value;
        }

        return json_encode($fakeData);
    }
}
