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
        Schema::create('bills', function (Blueprint $table) {
            $table->bigIncrements('bill_id');
            $table->bigInteger('costomer_id', false, true);
            $table->integer('amount', false, true);
            $table->integer('member_id', false, true);
            $table->boolean('share');
            $table->dateTime('bill_date');
            $table->integer('order_id_1', false, true);
            $table->integer('order_id_2', false, true);
            $table->integer('order_id_3', false, true);
            $table->integer('order_id_4', false, true);
            $table->integer('order_id_5', false, true);
            $table->integer('order_id_6', false, true);
            $table->integer('order_id_7', false, true);
            $table->integer('order_id_8', false, true);
            $table->integer('order_id_9', false, true);
            $table->integer('order_id_10', false, true);
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
        Schema::dropIfExists('bills');
    }
};
