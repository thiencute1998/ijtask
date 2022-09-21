<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SysTopMenu extends Model{
    protected $table = 'sys_top_menu';
    protected $primaryKey = 'MenuID';

    protected $fillable = [
        'MenuName',
        'MenuDescription',
        'RouterFrontEnd',
        'Position',
        'Status',
        'Module',
        'FeatureKey',
        'FeatureID'

    ];

    public $timestamps = false;

}

?>