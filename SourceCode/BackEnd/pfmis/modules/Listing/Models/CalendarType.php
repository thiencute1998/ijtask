<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class CalendarType extends Model{
    protected $table = 'calendar_type';
    protected $primaryKey = 'CalendarTypeID';

    public static function search(Request $request){
        $data = null;
        $query = Company::query();
        $where = [];
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('CompanyName')){
            $query->where('CompanyName', 'like', '%'.$request->CompanyName.'%');
        }
        if($request->post('CompanyNo')){
            $query->whereRaw('concat(Prefix, CompanyNo, Suffix) like("%'.$request->CompanyNo.'%")');
        }
        if($request->post('Fax')){
            $query->where('Fax', 'like', $request->Fax.'%');
        }
        if($request->post('Tel')){
            $query->where('Tel', 'like', $request->Tel.'%');
        }
        if($request->post('Email')){
            $query->where('Email', 'like', $request->Email.'%');
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->orderBy(DB::raw('CONCAT(Prefix, CompanyNo, Suffix)'))->paginate($per_page);
    }

    protected $fillable = [
        'CalendarName',
        'CalendarLevel',
        'HoursDay',
        'HoursSaturday',
        'WorkShiftID',
        'isDefault',
    ];
    public $timestamps = false;


}

?>