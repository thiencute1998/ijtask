<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Module\Listing\Traits\SbiCategoryCacheClear;
use Module\SysAdmin\Models\SysSetup;
use Module\SysAdmin\Models\SysUser;
use phpDocumentor\Reflection\Types\Self_;

class SbiCategory extends Model{
    use IjFullTextSearch, SbiCategoryCacheClear;
    protected $table = 'sbi_category';
    protected $primaryKey = 'SbiCategoryID';

    protected static $searchable = ['SbiCategoryNo', 'SbiCategoryName' ];

    public static function search(Request $request){
        $data = null;
        $query = SbiCategory::query();
        $where = [];
        $auth = Auth::user();
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        if($request->post('SbiCategoryName')){
            $query->where('SbiCategoryName', 'like', '%'.$request->SbiCategoryName.'%');
        }
        if($request->post('SbiCategoryNo')){
            $query->where('SbiCategoryNo', 'like', '%'.$request->SbiCategoryNo.'%');
        }


        //Danh mục liên kết
        $SbiCategoryLink = $request->post('SbiCategoryLink');
        if($SbiCategoryLink && count($SbiCategoryLink) > 0){
            $query->leftJoin('sbi_category_link','sbi_category.SbiCategoryID','=','sbi_category_link.SbiCategoryID');
            foreach($SbiCategoryLink as $sbiCategoryLink){
                if ($sbiCategoryLink['LinkTable']) {
                    $query->where('sbi_category_link.LinkTable', '=', $sbiCategoryLink['LinkTable']);
                }
                if ($sbiCategoryLink['LinkID']) {
                    $query->where('sbi_category_link.LinkID', '=', $sbiCategoryLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

//        // Quyền xem
//        $query->leftJoin('sbiCategory_per','sbiCategory_per.SbiCategoryID','=','sbiCategory.SbiCategoryID');
//        $query->where(function ($q) use ($auth){
//            $q->where('sbiCategory_per.EmployeeID',$auth->EmployeeID)
//                ->orWhere('sbiCategory.AccessType',2);
//        });
        if ($request->has('exportData')) {
            $query->select('sbi_category.SbiCategoryID','sbi_category.SbiCategoryNo', 'sbi_category.SbiCategoryName','sbi_category.STT');
            return $query->orderBy('SbiCategoryID', 'asc')->paginate($request->post('per_page'));
        }
        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        if($request->post('viewType') === 'tree'){
            $query-> where('sbi_category.Level', '=', 1);
            $query-> groupBy('sbi_category.SbiCategoryID')->orderBy('sbi_category.SbiCategoryNo');
            return $query->paginate($per_page);
        }
        $query->groupBy('sbi_category.SbiCategoryID');
        return $query->orderBy(DB::raw('SbiCategoryNo'))->paginate($per_page);
    }

    public static function getSbiCategory($request)
    {
        $perPage = ($request->get('per_page')) ? $request->get('per_page') : SysSetup::getOption('NumberRowOnPage');
        $query = self::query();
        if ($request->get('SbiCategoryName')) {
            $sbiCategoryName = $request->get('SbiCategoryName');
            $query->where('SbiCategoryName', 'like', '%' . $sbiCategoryName . '%');
        }

        $query->orderBy('SbiCategoryID', 'DESC');
        $sbiCategory = $query->paginate($perPage);
        return $sbiCategory;
    }

    public static function getAllChildSbiCategory($listID){
        $result = [];
        $sbiCategorys = SbiCategory::whereIn('ParentID', $listID)->get();
        if (count($sbiCategorys)) {
            $sbiCategoryIDs = [];
            foreach ($sbiCategorys as $sbiCategory) {
                $sbiCategoryIDs[] = $sbiCategory->SbiCategoryID;
                $result[] = $sbiCategory;
            }
            $records = SbiCategory::getAllChildSbiCategory($sbiCategoryIDs);
            $result = array_merge($result, $records);
        }

        return $result;
    }

    protected $fillable = [
        'SbiCategoryID',
        'SbiCategoryNo',
        'SbiCategoryName',
        'ParentID',
        'Level',
        'Detail',
        'NumberValue',
        'Note',
    ];
    public $timestamps = false;


}

?>
