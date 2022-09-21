<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CustomerVideo extends Model{
    protected $table = 'customer_video';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'CustomerID',
        'VideoID',
        'VideoName',
        'Description',
        'VideoType',
        'VideoSize',
        'DateModified',
        'UserModified',
        'DateModifiedRoot',
        'Link',
        'FileID',
        'DocID',
        'DocNo',
        'DocName'
    ];
    public $timestamps = false;

}

?>