<?php

use App\Models\TemporarySession;
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
        Schema::create('landing_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->unique()
                ->comment('セッション毎にユニークなトークン');
            $table->unsignedInteger('shop_id');
            $table->foreignId('customer_id')->nullable()->constrained()
                ->comment('LINE認証完了時の顧客ID (既に友だち追加済みの場合も含まれる)');
            $table->string('conversion_status', 32)->nullable()
                ->comment('コンバージョンステータス');
            $table->json('parameters')
                ->comment('ランディング時のクエリパラメータ');
            $table->text('referrer')->nullable()
                ->comment('ランディング時のリファラ');
            $table->dateTime('expired_at')
                ->comment('セッションが無効になった日時 (=LINE認証が完了した日時)');
            $table->timestamps();

            $table->foreign('shop_id')->references('shop_id')->on('shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('landing_sessions');
    }
};
