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
            $table->integer('amount', false, true);
            $table->foreignId('shop_id', false, true);
            $table->foreignId('member_id', false, true);
            $table->foreignId('bill_order_id', false, true);
            $table->boolean('share');
            $table->tinyInteger('top_change', false, true);
            $table->dateTime('bill_date');
            $table->timestamps();
            $table->softDeletes();
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
