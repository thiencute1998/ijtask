<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterObjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('object', function (Blueprint $table) {
            DB::statement('ALTER TABLE object ADD FULLTEXT search (ObjectNo, ObjectName, Address, OfficePhone, Fax, Email, Website, Note)'); //đánh index cho cột name
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
        Schema::table('object', function (Blueprint $table) {
            DB::statement('ALTER TABLE object DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
        });
    }
}
