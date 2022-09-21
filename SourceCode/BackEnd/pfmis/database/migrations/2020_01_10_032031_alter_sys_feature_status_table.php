<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSysFeatureStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sys_feature_status', function (Blueprint $table) {
            DB::statement('ALTER TABLE sys_feature_status ADD FULLTEXT search (StatusName)'); //đánh index cho cột name
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('sys_feature_status', function (Blueprint $table) {
            DB::statement('ALTER TABLE sys_feature_status DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
        });
    }
}
