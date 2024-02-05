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
        Schema::create('codes', function (Blueprint $table) {
            $table->bigIncrements('code_id');
            $table->bigInteger('shop_id', false, true);
            $table->bigInteger('situation_id', false, true);
            $table->string('name', 100);
            $table->string('hash', 10);
            $table->string('parameter');
            $table->integer('kind');
            $table->longText('script');
            $table->longText('notes')->default('');
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
        Schema::dropIfExists('codes');
    }
};
