<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BoxWeightSeeder extends Seeder
{
    public const ORDERS_IDS = [10724512,10724515];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $weightExpected = $faker->randomNumber(4);
        foreach (self::ORDERS_IDS as $orderId) {
            DB::table('box_weight')->insert([
                'measured_at' => $faker->randomNumber(5),
                'message_number' => $faker->randomNumber(5),
                'weight_expected' => $weightExpected,
                'weight_real' => $weightExpected + $faker->numberBetween(-100, 100),
                'weight_expected_unit' => 'g',
                'weight_real_unit' => 'g',
                'order_id' => $orderId,
                'box_uid' => $faker->randomNumber(6),
                'picker_id' => $faker->randomNumber(3)
            ]);
        }
    }
}
