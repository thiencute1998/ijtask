<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class SysStatusItem extends Model{
    protected $table = 'sys_status_item';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = SysStatusItem::query();
        $where = [];
        if($request->post('StatusDescription')){
            $query->where('StatusDescription', 'like', $request->StatusDescription . '%');
        }

        if ($request->post('extraParams')) {
            foreach ($request->post('extraParams') as $key => $value) {
                $query->where($key, '=', $value);
            }
        }

        if ($request->post('StatusID')) {
            $query->where('StatusID', '=', $request->post('StatusID'));
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('NOrder')->paginate($per_page);
    }

    protected $fillable = [
        'StatusID',
        'DataType',
        'StatusValue',
        'StatusDescription',
        'ExecutionStatus',
        'Norder',

    ];

    public static function getItemsByStatusID($statusID){
        $query = self::query();
        $query->select('StatusValue', 'StatusDescription', 'DataType', 'ExecutionStatus')
            ->where('StatusID', '=', $statusID);
        $items = $query->get();
        return $items;
    }

    public $timestamps = false;

}

?>
