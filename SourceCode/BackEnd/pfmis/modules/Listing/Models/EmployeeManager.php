<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class EmployeeManager extends Model{
    protected $table = 'employee_manager';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = EmployeeManager::query();
        $where = [];
        if($request->post('ManagerName')){
            $query->where('ManagerName', 'like', $request->ManagerName.'%');
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy('LineID')->paginate($per_page);
    }

    protected $fillable = [
        'ManagerID',
        'ManagerName',
        'EmployeeID',
        'EmployeeName',
        'IsDirectManager',
        'IsConcurrentlyManager',
        'CompanyName',
        'PositionName'
    ];
    public $timestamps = false;


}

?>
