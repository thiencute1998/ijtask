<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatContentUserRead extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_content_user_read';
    protected $primaryKey = 'LineID';

    protected static $searchable = ['FieldAttach', 'FileAttachName'];

    public static function search(Request $request){

    }

    protected $fillable = [
        'ChatContentID', 'UserID', 'username', 'EmployeeID', 'EmployeeName', 'ReadDate'
    ];
    public $timestamps = false;


}

?>
