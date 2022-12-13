<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->integer('flavor_id_1', false, true);
            $table->integer('flavor_id_2', false, true);
            $table->integer('flavor_id_3', false, true);
            $table->integer('flavor_id_4', false, true);
            $table->integer('flavor_id_5', false, true);
            $table->integer('flavor_id_6', false, true);
            $table->integer('flavor_id_7', false, true);
            $table->integer('flavor_id_8', false, true);
            $table->integer('flavor_id_9', false, true);
            $table->integer('flavor_id_10', false, true);
            $table->tinyInteger('top_change', false, true);
            $table->dateTime('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
