<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class ContractFile extends Model{
    protected $table = 'contract_file';
    protected $primaryKey = 'LineID';


    protected $fillable = [
        'ContractID',
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