<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatContentFile extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_content_file';
    protected $primaryKey = 'LineID';

    protected static $searchable = ['FieldAttach', 'FileAttachName'];

    public static function search(Request $request){

    }

    protected $fillable = [
        'ChatContentID', 'FileID', 'FieldAttach', 'FileAttachName', 'FileType'
    ];
    public $timestamps = false;


}

?>
