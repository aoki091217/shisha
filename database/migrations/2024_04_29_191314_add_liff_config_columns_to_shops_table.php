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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('liff_channel_id', 64)->nullable()
                ->comment('LineログインのチャネルID (LINEログイン > チャネル基本設定 > チャネルID)')
                ->after('channel_secret');
            $table->string('liff_id', 64)->nullable()
                ->comment('LIFF ID (LINEログイン > LIFF > LIFFアプリ詳細 > LIFF ID)')
                ->after('liff_channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('liff_channel_id', 'liff_id');
        });
    }
};
