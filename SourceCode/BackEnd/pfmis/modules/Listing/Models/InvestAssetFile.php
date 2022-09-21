<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class InvestAssetFile extends Model{
    protected $table = 'invest_asset_file';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = InvestAssetFile::query();
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'InvestAssetID',
        'FileID',
        'FileName',
        'Description',
        'FileType',
        'FileSize',
        'DateModified',
        'UserModified',
        'Link'
    ];
    public $timestamps = false;

}

?>
