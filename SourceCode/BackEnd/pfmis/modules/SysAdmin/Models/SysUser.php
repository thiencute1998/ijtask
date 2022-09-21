<?php
namespace Module\SysAdmin\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class SysUser extends Authenticatable{
    use HasApiTokens, Notifiable, IjFullTextSearch;
    protected $table = 'sys_user';
    protected $primaryKey = 'UserID';

    protected static $searchable = ['FullName', 'username', 'Note'];

    public static function search(Request $request){
        $data = null;
        $query = SysUser::query();
        $where = [];

        if($request->post('FullName')){
            $query->where('FullName', 'like', '%'.$request->FullName.'%');
        }
        if($request->post('username')){
            $query->where('username', 'like', '%'.$request->username.'%');
        }
        if($request->post('Inactive') === 1 || $request->get('Inactive') === 0){
            $query->where('Inactive', $request->Inactive);
        }
        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        return $query->paginate($per_page);
    }

    protected $fillable = [
        'username', 'password', 'FullName', 'UserType', 'NOrder', 'Note', 'Avata', 'EmployeeID', 'EmployeeNo'
    ];
    public $timestamps = false;

    public static function checkPer($feature, $action){
        $arrPer = json_decode(Session::get('per_feature'), true);
        $check = false;
        if(isset($arrPer[$feature])){
            switch ($action){
                case 'access':
                case 'index':
                    $action = 'Access';
                    if($arrPer[$feature][$action] == 1){
                        $check = true;
                    }
                    break;
                case 'addnew':
                case 'new':
                case 'store':
                    $action = 'Addnew';
                    if($arrPer[$feature][$action] == 1){
                        $check = true;
                    }
                    break;
                case 'edit':
                case 'update':
                    $action = 'Edit';
                    if($arrPer[$feature][$action] == 1){
                        $check = true;
                    }
                    break;
                case 'delete':
                    $action = 'Delete';
                    if($arrPer[$feature][$action] == 1){
                        $check = true;
                    }
                    break;
                default:
                    $check = true;
                    break;
            }
        }else{
            $check = true;
        }
        return $check;
    }

    public static function getAllUsers(Request $request){
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        $query->select('UserID', 'FullName', 'username', 'Note');
        $term = $request->get('term');
        if ($term) {
            $query->where('FullName', 'like', '%' . $term . '%');
            $query->orWhere('username', 'like', '%' . $term . '%');
        }
        $users = $query->paginate($perPage);
        return $users;
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getPerModule($TaskID, $TablePer){

    }
}

?>
