<?php
namespace Module\Listing\Models;

use IjCore\IjFullTextSearch;
use Illuminate\Database\Eloquent\Model;

class NormAllotMap extends Model{
    use IjFullTextSearch;
    protected $table = 'norm_allot_map';
    protected $primaryKey = 'LineID';

    protected $fillable = ['NormAllotID', 'NormAllotNo', 'NormAllotName', 'NormID', 'NormNo', 'NormName'];
    public $timestamps = false;

}

?>
