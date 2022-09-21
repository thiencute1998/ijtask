<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class BankLink extends Model{
    protected $table = 'bank_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'BankID',
        'LinkNo',
        'LinkName',
        'LinkTable'
    ];
    public $timestamps = false;


}

?>
