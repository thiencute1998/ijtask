<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Module\Listing\Traits\SbiChapterCacheClear;
use Module\SysAdmin\Models\SysSetup;

class SbiChapter extends Model{
    use IjFullTextSearch, SbiChapterCacheClear;
    protected $table = 'sbi_chapter';
    protected $primaryKey = 'SbiChapterID';

    protected static $searchable = ['SbiChapterNo', 'SbiChapterName'];

    public static function search(Request $request){
        $data = null;
        $query = SbiChapter::query()->select('sbi_chapter.*');
        $auth = Auth::user();
        if($request->post('SbiChapterName')){
            $query->where('SbiChapterName', 'like', '%' . $request->SbiChapterName . '%');
        }
        if($request->post('SbiChapterNo')){
            $query->whereRaw('SbiChapterNo like("%' . $request->SbiChapterNo . '%")');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }

        // Danh mục liên kết
        $sbi_chapterLinks = $request->post('SbiChapterLink');
        if ($sbi_chapterLinks && (count($sbi_chapterLinks) > 0)) {
            $query->join('sbi_chapter_link', 'sbi_chapter.SbiChapterID', '=', 'sbi_chapter_link.SbiChapterID');
            foreach ($sbi_chapterLinks as $sbi_chapterLink) {
                if ($sbi_chapterLink['LinkTable']) {
                    $query->where('sbi_chapter_link.LinkTable', '=', $sbi_chapterLink['LinkTable']);
                }
                if ($sbi_chapterLink['LinkID']) {
                    $query->where('sbi_chapter_link.LinkID', '=', $sbi_chapterLink['LinkID']);
                }
            }
        }

        $fullTextSearch = ($request->post('fullTextSearch')) ? $request->post('fullTextSearch') : '';
        if ($fullTextSearch && $fullTextSearch !== '' && (strlen($fullTextSearch) > 1)) {
            $query = self::scopeSearch($query, $fullTextSearch, self::$searchable);
        }

        // quyền xem tài liệu
        $query->leftJoin('sbi_chapter_per', 'sbi_chapter.SbiChapterID', '=', 'sbi_chapter_per.SbiChapterID');
        $query->where(function ($q) use ($auth) {
            $q->where('sbi_chapter_per.EmployeeID', '=', $auth->EmployeeID);
            $q->orWhere(function ($q) {
                $q->where('sbi_chapter.AccessType', '=', 2);
            });
        });

        if($request->post('per_page')){
            $per_page = $request->post('per_page');
        }else{
            $per_page = SysSetup::getOption('NumberRowOnPage');
        }
        $query->groupBy('sbi_chapter.SbiChapterID');

        if ($request->has('exportData')) {
            $query->select('sbi_chapter.SbiChapterID','sbi_chapter.SbiChapterNo', 'sbi_chapter.SbiChapterName','sbi_chapter.STT');
            return $query->orderBy('SbiChapterID', 'asc')->paginate($request->post('per_page'));
        }

        return $query->orderBy('SbiChapterID')->paginate($per_page);
    }

    protected $fillable = [
        'SbiChapterNo',
        'SbiChapterName',
        'Note',
        'AccessType',
        'UserIDCreated',
        'AuthorizedPerson',
        'Locked',
        'Inactive'
    ];
    public $timestamps = false;


}

?>
