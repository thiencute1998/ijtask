<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class FixedAssetVideo extends Model{
    protected $table = 'fixed_asset_video';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = VendorVideo::query();
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'FixedAssetID',
        'VideoID',
        'VideoName',
        'Description',
        'VideoType',
        'VideoSize',
        'DateModified',
        'UserModified',
        'Link'
    ];
    public $timestamps = false;

}

?>
