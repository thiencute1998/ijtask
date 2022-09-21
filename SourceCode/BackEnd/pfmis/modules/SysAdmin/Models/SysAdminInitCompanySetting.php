<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SysAdminInitCompanySetting extends Model{
    protected $table = 'sys_admin_init_company_setting';
    protected $primaryKey = 'SettingID';

    protected $fillable = [
        'SettingKey',
        'SettingName',
        'SettingValue',
        'DataType',
        'AllowChange',
        'SettingValueMeta',
        'ModuleID',
        'NOrder',
        'Inactive'
    ];

    public $timestamps = false;
}

?>
