<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_member';
    protected $primaryKey = 'MemberID';

    protected static $searchable = ['UserName'];

    public static function search(Request $request){

    }

    protected $fillable = [
        'UserName', 'UserID', 'GroupID', 'DateJoin', 'DateDelete', 'Type', 'Status', 'NumberNotRead'
    ];
    public $timestamps = false;


}

?>
