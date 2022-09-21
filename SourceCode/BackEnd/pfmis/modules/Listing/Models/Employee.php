<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Employee extends Model{
    use IjFullTextSearch;
    protected $table = 'employee';
    protected $primaryKey = 'EmployeeID';

    protected static $searchable = ['EmployeeNo', 'EmployeeName', 'employee.PositionName', 'employee.OfficePhone', 'employee.HandPhone', 'employee.Email', 'employee.FacebookID', 'employee.TwitterID', 'employee.SkypeID', 'employee.ZaloID', 'employee.Note'];

    public static function search(Request $request){
        $data = null;
        $query = Employee::query();
        $where = [];
        $query->select('employee.*', 'company.CompanyName');
        $query->leftJoin('company', 'company.CompanyID', '=', 'employee.CompanyID');

        if($request->post('EmployeeName')){
            $query->where('EmployeeName', 'like', '%'.$request->EmployeeName.'%');
        }
        if($request->post('EmployeeNo')){
            $query->whereRaw('concat(employee.Prefix, EmployeeNo, employee.Suffix) like("%'.$request->EmployeeNo.'%")');
        }
        if($request->post('HandPhone')){
            $query->where('employee.HandPhone', 'like', $request->HandPhone.'%');
        }
        if($request->post('Email')){
            $query->where('employee.Email', 'like', $request->Email.'%');
        }

        if($request->post('PositionName')){
            $query->where('employee.PositionName', 'like', '%' . $request->PositionName . '%');
        }

        if($request->post('CompanyName')){
            $query->where('company.CompanyName', 'like', '%' . $request->CompanyName . '%');
        }

        if(isset($request->Inactive)){
            $query->where('employee.Inactive', '=', $request->post('Inactive'));
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        $term = $request->post('term');
        if ($term) {
            $query->where(function($q) use ($term){
                $q->where('EmployeeName', 'like', '%' . $term . '%')
                ->orWhere('EmployeeNo', 'like', '%' . $term . '%');
            });
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }

        if ($request->has('exportData')) {
            $query->select('employee.EmployeeID','employee.EmployeeNo', 'employee.EmployeeName', 'employee.PositionName','company.CompanyName', 'employee.Email','employee.STT');

            return $query->orderBy('EmployeeID', 'asc')->paginate($request->post('per_page'));
        }
        return $query->orderBy('EmployeeID', 'desc')->paginate($per_page);
    }

    protected $fillable = [
        'EmployeeNo','FirstName','MiddleName','LastName','EmployeeName', 'CompanyID' ,'CompanyName', 'DepartmentID','DepartmentNo', 'DepartmentName',
        'PositionName', 'BirthDay', 'CitizenIdNo', 'CitizenIdDate', 'CitizenIdAt', 'OfficePhone', 'HandPhone', 'Email',
        'FacebookID', 'TwitterID', 'SkypeID', 'ZaloID', 'CheckerID', 'Note', 'UserID',
        'NOrder', 'Inactive', 'Path', 'Level', 'NumberValue', 'ShortName'
    ];
    public $timestamps = false;

    public static function getEmployee(Request $request){
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        $query->select('UserID', 'EmployeeID','EmployeeName');
        $term = $request->get('term');
        if ($term) {
            $query->where(function($q) use ($term){
                $q->where('EmployeeName', 'like', '%' . $term . '%')
                    ->orWhere('EmployeeNo', 'like', '%' . $term . '%');
            });
        }
        $users = $query->paginate($perPage);
        return $users;
    }

}

?>
