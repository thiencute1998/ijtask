<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class SysTable extends Model{
    protected $table = 'sys_table';
    protected $primaryKey = 'TableID';

    protected $fillable = [
        'TableName',
        'TableDescription',
        'isTaskDatalist',
        'TaskDatalistOrder',
    ];

    public $timestamps = false;

}

?>