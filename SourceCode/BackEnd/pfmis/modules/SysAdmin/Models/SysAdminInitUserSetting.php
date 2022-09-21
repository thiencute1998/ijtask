<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;

class SysAdminInitUserSetting extends Model{
    protected $table = 'sys_admin_user_setting';
    protected $primaryKey = 'SettingID';

    protected $fillable = [
        'SettingKey',
        'SettingName',
        'SettingValue',
        'DataType',
        'AllowChange',
        'AllowDisplay',
        'SettingValueMeta',
        'ModuleID',
        'NOrder',
        'Inactive'
    ];

    public $timestamps = false;
}

?>
