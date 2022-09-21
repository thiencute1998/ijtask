<?php
namespace Module\Customer\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Module\SysAdmin\Models\SysSetup;

class Opportunity extends Model{
    use IjFullTextSearch;
    protected $table = 'customer_opportunity';
    protected $primaryKey = 'OpportunityID';

    protected static $searchable = ['OpportunityName'];

    public static function search(Request $request){
        $data = null;
        $query = Opportunity::query();
        $where = [];
        if($request->post('OpportunityName')){
            $query->where('OpportunityName', 'like', $request->OpportunityName.'%');
        }
        if(isset($request->Inactive)){
            $query->where('Inactive', '=', $request->post('Inactive'));
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
        'OpportunityName', 'CustomerID', 'CustomerName','EmployeeID', 'EmployeeName', 'OpportunityDate', 'ExpectedDate', 'OTAmount', 'Inactive'
    ];
    public $timestamps = false;


}

?>
