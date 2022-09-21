<?php
namespace Module\Extensions\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ChatTag extends Model{
    use IjFullTextSearch;
    protected $table = 'chat_tag';
    protected $primaryKey = 'TagID';

    protected static $searchable = ['TagName', 'TagSlug'];

    public static function search(Request $request){

    }

    protected $fillable = [
        'TagName', 'TagSlug'
    ];
    public $timestamps = false;


}

?>
