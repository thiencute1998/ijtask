<?php
namespace Module\Customer\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model{
    protected $table = 'customer_file';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = CustomerFile::query();
        return $query->paginate(20);
    }

    protected $fillable = [
        'CustomerID',
        'TransID',
        'TransTable',
        'FileID',
        'FileName',
        'Description',
        'FileType',
        'FileSize',
        'DateModified',
        'UserModified',
        'Link',
        'DateModifiedRoot',
        'FileNameRoot',
        'DocID',
        'DocNo',
        'DocName'
    ];
    public $timestamps = false;

}

?>
