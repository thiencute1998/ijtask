<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class CoaLdiLink extends Model{
    protected $table = 'coa_ldi_link';
    protected $primaryKey = 'LineID';
    public static function search(Request $request, $AccountID){
        return 1;
    }
    protected $fillable = [
        'LineID',
        'AccountID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable'
    ];
    public $timestamps = false;
}
?>
