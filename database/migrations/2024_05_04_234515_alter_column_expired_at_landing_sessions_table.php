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
        Schema::table('landing_sessions', function (Blueprint $table) {
            $table->dropColumn(['expired_at']);
        });
        Schema::table('landing_sessions', function (Blueprint $table) {
            $table->dateTime('expired_at')->nullable()->after('referrer')
                ->comment('セッションが無効になった日時 (=LINE認証が完了した日時)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('landing_sessions', function (Blueprint $table) {
            $table->dropColumn(['expired_at']);
        });
        Schema::table('landing_sessions', function (Blueprint $table) {
            $table->dateTime('expired_at')->after('referrer')
                ->comment('セッションが無効になった日時 (=LINE認証が完了した日時)');
        });
    }
};
