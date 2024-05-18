<?php

use App\Models\CustomerShop;
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
        Schema::table('customer_shops', function (Blueprint $table) {
            $table->dropColumn(['friend_status', 'liff_status', 'activated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_shops', function (Blueprint $table) {
            $table->string('friend_status', 16)->default('unknown')
                ->comment('友だち登録のステータス')
                ->after('shop_id');
            $table->string('liff_status', 16)->default('unknown')
                ->comment('LIFF認証のステータス')
                ->after('friend_status');
            $table->dateTime('activated_at')->nullable()
                ->comment('初めて友だち登録とLIFF認証が有効になった日時')
                ->after('liff_status');
        });
    }
};
