<?php
namespace Module\Sbpmakeplan\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Module\SysAdmin\Models\SysSetup;

class Actautoact extends Model{
    protected $table = 'act_autoact';

    public static function search(Request $request, $TransID){
        return 1;
    }
    protected $fillable = [
        'AutoactName',
        'DebitAccountID',
        'DebitAccountNo',
        'CreditAccountID',
        'CreditAccountNo',
        'ConDescription',
        'SnaDebitAccountID',
        'SnaDebitAccountNo',
        'SnaCreditAccountID',
        'SnaCreditAccountNo',
        'SnaDescription',
        'TabDebitAccountID',
        'TabDebitAccountNo',
        'TabCreditAccountID',
        'TabCreditAccountNo',
        'TabDescription',
        'AnuDebitAccountID',
        'AnuDebitAccountNo',
        'AnuCreditAccountID',
        'AnuCreditAccountNo',
        'AnuDescription',
        'PmuDebitAccountID',
        'PmuDebitAccountNo',
        'PmuCreditAccountID',
        'PmuCreditAccountNo',
        'PmuDescription',
        'ScbDebitAccountID',
        'ScbDebitAccountNo',
        'ScbCreditAccountID',
        'ScbCreditAccountNo',
        'ScbDescription',
        'Norder',
        'Inactive',
    ];
    public $timestamps = false;

}

?>
