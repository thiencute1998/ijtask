<?php
namespace Module\SysAdmin\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatMember extends Model{
    protected $table = 'chat_member';
    protected $primaryKey = 'MemberID';
    protected $fillable = [
        'UserName',
        'UserID',
        'GroupID',
        'GroupNo',
        'DateJoin',
        'Type',
        'Status',
        'NumberNotRead'

    ];
    public $timestamps = false;

}

?>