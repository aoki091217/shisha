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
        Schema::create('customer_shop_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id');
            $table->foreignId('customer_id')->constrained();
            $table->string('friend_status', 16)->default('unknown')
                ->comment('友だち登録のステータス');
            $table->string('liff_status', 16)->default('unknown')
                ->comment('LIFF認証のステータス');
            $table->dateTime('activated_at')->nullable()
                ->comment('初めて友だち登録とLIFF認証が有効になった日時');
            $table->dateTime('first_visited_at')->nullable()
                ->comment('初回来店日時');
            $table->dateTime('recently_visited_at')->nullable()
                ->comment('直近の来店日時');
            $table->timestamps();

            $table->foreign('shop_id')->references('shop_id')->on('shops');
            $table->unique(['shop_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_shop_statuses');
    }
};
