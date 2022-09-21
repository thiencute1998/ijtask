<?php

namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Module\SysAdmin\Models\SysSetup;

class ProjectCateList extends Model
{
    use IjFullTextSearch;

    public $table = 'project_cate_list';
    protected $primaryKey = 'CateID';
    protected $guarded= [];
    public $timestamps = false;
    protected static $searchable = ['CateName'];

    public static function search($request){
        $query = ProjectCateList::query();

        if($request->post('CateName')){
            $query->where('CateName','like','%'. $request->post('CateName'). '%');
        }
        if($request->post('CateNo')){
            $query->where('CateNo','like','%'. $request->post('CateNo'). '%');
        }

        if($request->post('Inactive')){
            $query->where('Inactive',$request->post('Inactive'));
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
        if($request->post('viewType') === 'tree'){
            $query-> where('Level', '=', 1);
            $query-> groupBy('CateID');
            return $query->orderBy('CateNo', 'ASC')->paginate($per_page);
        }
        return $query->orderBy('CateNo')->paginate($per_page);
    }
}
