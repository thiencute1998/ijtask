<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('employee', function (Blueprint $table) {
            DB::statement('ALTER TABLE employee ADD FULLTEXT search (EmployeeNo, EmployeeName, `Position`, OfficePhone, HandPhone, Email, FacebookID, TwitterID, SkypeID, ZaloID, Note)'); //đánh index cho cột name
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
        Schema::table('employee', function (Blueprint $table) {
            DB::statement('ALTER TABLE employee DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
        });
    }
}
