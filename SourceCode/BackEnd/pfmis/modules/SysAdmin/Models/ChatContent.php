<?php
namespace Module\SysAdmin\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatContent extends Model{
    protected $table = 'chat_content';
    protected $primaryKey = 'LineID';
    protected $fillable = [
        'GroupNo',
        'GroupID',
        'UserName',
        'UserID',
        'CreatedDate',
        'Content',
        'Attach',
        'UserRead'

    ];
    public $timestamps = false;

}

?>