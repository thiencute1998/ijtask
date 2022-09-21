<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SysAutoNumber extends Model{
    protected $table = 'sys_autonumber_setting';
    protected $primaryKey = 'NumberID';

    protected $fillable = [
        'NumberKey',
        'NumberName',
        'NumberValue',
        'Prefix',
        'Suffix',
        'AllowChange',
        'NumberMask',
        'PadLeng',
        'PadLeng1',
        'PadLeng1',
        'PadLeng2',
        'PadLeng3',
        'PadLeng4',
        'PadLeng5',
        'PadLeng6',
        'PadLeng7',
        'PadLeng8',
        'PadLeng9',
        'SignChar',
        'MaxLevel'

    ];
    public $timestamps = false;


}

?>