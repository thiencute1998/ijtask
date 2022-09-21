<?php
namespace Module\SysAdmin\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatGroup extends Model{
    protected $table = 'chat_group';
    protected $primaryKey = 'GroupID';
    protected $fillable = [
        'GroupNo',
        'GroupName',
        'CategoryID',
        'CategoryType',
        'Created',
        'CreatedDate',
    ];
    public $timestamps = false;

}

?>