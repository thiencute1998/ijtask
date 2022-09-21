<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ContractVideo extends Model{
    protected $table = 'contract_video';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TaskID',
        'VideoID',
        'VideoName',
        'Description',
        'VideoType',
        'VideoSize',
        'DateModified',
        'UserModified',
        'DateModifiedRoot',
        'FileNameRoot',
        'Link',
        'FileID',
        'DocID',
        'DocNo',
        'DocName'
    ];
    public $timestamps = false;

}

?>