<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatContentTag extends Model{
    protected $table = 'chat_content_tag';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){

    }

    protected $fillable = [
        'ChatContentID', 'TagID'
    ];
    public $timestamps = false;


}

?>
