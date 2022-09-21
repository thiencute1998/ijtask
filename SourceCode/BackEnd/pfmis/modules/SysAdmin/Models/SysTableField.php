<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class SysTableField extends Model{
    protected $table = 'sys_table_field';
    protected $primaryKey = 'FieldID';

    protected $fillable = [
        'TableID',
        'FieldName',
        'TableName',
        'FieldDescription',
        'DataType',
        'DataWidth',
        'TaskObjectOrder',
    ];

    public $timestamps = false;

}

?>
