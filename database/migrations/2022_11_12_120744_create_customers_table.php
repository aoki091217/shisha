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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigInteger('customer_id', true, true);
            $table->string('line_token')->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('sex', false, true)->nullable();
            $table->integer('generation', false, true)->nullable();
            $table->string('reason')->nullable();
            $table->date('customer_date')->nullable();
            $table->tinyInteger('step', false, true)->nullable();
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
        Schema::dropIfExists('customers');
    }
};
