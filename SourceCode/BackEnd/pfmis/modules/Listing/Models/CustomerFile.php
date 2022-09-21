<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CustomerFile extends Model{
    protected $table = 'customer_file';
    protected $primaryKey = 'LineID';


    protected $fillable = [
        'CustomerID',
        'FileID',
        'FileName',
        'Description',
        'FileType',
        'FileSize',
        'DateModified',
        'UserModified',
        'Link',
        'DocID',
        'DocNo',
        'DocName',
        'DateModifiedRoot',
        'FileNameRoot',
    ];
    public $timestamps = false;

}

?>