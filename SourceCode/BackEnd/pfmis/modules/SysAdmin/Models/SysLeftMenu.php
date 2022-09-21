<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SysLeftMenu extends Model{
    protected $table = 'sys_left_menu';
    protected $primaryKey = 'MenuID';

    protected $fillable = [
        'MenuName',
        'MenuDescription',
        'RouterFrontEnd',
        'ParentID',
        'Level',
        'Status',
        'Module',
        'FeatureKey',
        'FeatureID'

    ];

    public $timestamps = false;

}

?>