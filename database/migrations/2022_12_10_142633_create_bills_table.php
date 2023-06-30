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
            $table->integer('amount', false, true)->nullable();
            $table->foreignId('shop_id', false, true)->nullable();
            $table->foreignId('member_id', false, true)->nullable();
            $table->boolean('share')->nullable();
            $table->boolean('top_change')->nullable();
            $table->dateTime('bill_date')->nullable();
            $table->boolean('is_draft')->default(1);
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
