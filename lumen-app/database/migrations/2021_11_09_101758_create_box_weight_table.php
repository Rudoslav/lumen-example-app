<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxWeightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_weight', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('measured_at', false, true);
            $table->bigInteger('message_number', false, true);
            $table->integer('weight_expected', false, true);
            $table->integer('weight_real', false, true);
            $table->string('weight_expected_unit', 5);
            $table->string('weight_real_unit', 5);
            $table->integer('order_id', false, true);
            $table->bigInteger('box_uid', false, true);
            $table->integer('picker_id', false, true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('box_weight');
    }
}
