<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SysAdminUserSetting extends Model{
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
        'Inactive',
        'UserID'
    ];

    public $timestamps = false;

    public static function getOption($optionKey){
        $content = Storage::disk('local')->get('config.json');
        $option = json_decode($content, true);
        return $option[$optionKey];
    }
}

?>
