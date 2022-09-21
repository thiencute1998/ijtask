<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;


class TreasuryLink extends Model{

    protected $table = 'treasury_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'TreasuryID', 'LinkNo', 'LinkName', 'LinkTable'
    ];
    public $timestamps = false;


}

?>
