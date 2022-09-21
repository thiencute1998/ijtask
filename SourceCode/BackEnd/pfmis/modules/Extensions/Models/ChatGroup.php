<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_group';
    protected $primaryKey = 'GroupID';

    protected static $searchable = ['GroupNo', 'GroupName'];

    public static function search(Request $request){

    }

    protected $fillable = [
        'GroupNo', 'GroupName', 'GroupDescription', 'GroupType', 'CategoryKey', 'Created', 'CreatedDate',
        'UpdatedDate', 'Locked', 'HideGroupChat', 'Deleted'
    ];
    public $timestamps = false;


}

?>
