<?php

namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Module\SysAdmin\Models\SysSetup;

class ItemCateList extends Model
{
    use IjFullTextSearch;

    public $table = 'item_cate_list';
    protected $primaryKey = 'CateID';
    protected $fillable= [
        'CateNo','CateName','ParentID','Level','Detail','isSystemSetting','Inactive'
    ];
    public $timestamps = false;
    protected static $searchable = ['CateName'];

    public static function search($request){
        $query = ItemCateList::query();

        if($request->post('CateName')){
            $query->where('CateName','like',$request->post('CateName'). '%');
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
        return $query->paginate($per_page);
    }



}
