<?php
/**
 * GymBeam s.r.o.
 *
 * Copyright © GymBeam, All rights reserved.
 *
 * @copyright Copyright © 2021 GymBeam (https://gymbeam.com/)
 * @category GymBeam
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullBoxWeightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('box_weight', function (Blueprint $table) {
            $table->bigInteger('measured_at', false, true)->nullable()->change();
            $table->bigInteger('message_number', false, true)->nullable()->change();
            $table->integer('weight_expected', false, true)->nullable()->change();
            $table->integer('weight_real', false, true)->nullable()->change();
            $table->string('weight_expected_unit', 5)->nullable()->change();
            $table->string('weight_real_unit', 5)->nullable()->change();
            $table->bigInteger('box_uid', false, true)->nullable()->change();
            $table->integer('picker_id', false, true)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
