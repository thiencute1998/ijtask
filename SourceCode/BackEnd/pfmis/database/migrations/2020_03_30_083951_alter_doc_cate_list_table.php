<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocCateListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('doc_cate_list', function (Blueprint $table) {
            DB::statement('ALTER TABLE doc_cate_list ADD FULLTEXT search (CateName)'); //đánh index cho cột name
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
        Schema::table('doc_cate_list', function (Blueprint $table) {
            DB::statement('ALTER TABLE doc_cate_list DROP INDEX search'); // khi chạy lệnh rollback thì làm điều ngược lại với thằng trên thế thôi
        });
    }
}
