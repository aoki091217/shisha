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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('situation_id');
            $table->boolean('type');
            $table->string('alt_text', 400)->nullable();
            $table->string('keyword', 50)->nullable();
            $table->string('thumbnail_image_url', 2000)->nullable();
            $table->string('title', 40)->nullable();
            $table->string('text', 5000);
            $table->boolean('turn');
            $table->boolean('send_type');
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
        Schema::dropIfExists('messages');
    }
};
