<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SysAdminGlobalSetting extends Model{
    protected $table = 'sys_admin_global_setting';
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
        'Inactive',
    ];

    public $timestamps = false;

    public static function getOption($optionKey){
        $content = Storage::disk('local')->get('config.json');
        $option = json_decode($content, true);
        return $option[$optionKey];
    }
}

?>
