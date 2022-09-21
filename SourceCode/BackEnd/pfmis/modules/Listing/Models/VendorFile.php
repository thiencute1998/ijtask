<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class VendorFile extends Model{
    protected $table = 'vendor_file';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = VendorFile::query();
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'VendorID',
        'FileID',
        'FileName',
        'Description',
        'FileType',
        'FileSize',
        'DateModified',
        'UserModified',
        'Link',
        'DateModifiedRoot',
        'FileNameRoot'
    ];
    public $timestamps = false;

}

?>
