<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class ContractComment extends Model{
    protected $table = 'contract_comment';
    protected $primaryKey = 'LineID';

    public static function search(Request $request){
        $data = null;
        $query = ContractComment::query();
        $where = [];
        if($request->post('ContractName')){
            $query->where('ContractName', 'like', $request->ContractName.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
        }
        return $query->paginate(SysSetup::getOption('NumberRowOnPage'));
    }

    protected $fillable = [
        'ContractID',
        'ContractName',
        'EmployeeID',
        'EmployeeName',
        'CommentDate',
        'CommentContent',
        'FileAttach',
        'IsImg',
        'FileAttachName',
        'ParentID'
    ];
    public $timestamps = false;

}

?>