<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Zend\Diactoros\Request;

class SysFeatureStatusItem extends Model{
    protected $table = 'sys_feature_status_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = SysFeatureStatusItem::query();
        $where = [];
        if($request->post('StatusDescription')){
            $query->where('StatusDescription', 'like', $request->StatusDescription.'%');
        }
        if($request->post('Inactive')){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('NOrder')->paginate($per_page);
    }

    protected $fillable = [
        'FeatureID',
        'StatusID',
        'StatusValue',
        'StatusDescription',
        'DataType',
        'NOrder',
    ];

    public static function getItemsByStatusID($statusID){
        $query = self::query();
        $query->select('StatusValue', 'StatusDescription', 'DataType')
            ->where('StatusID', '=', $statusID);

        $items = $query->get();
        return $items;
    }

    public $timestamps = false;

}

?>