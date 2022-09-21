<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SysInitSetting extends Model{
    protected $table = 'sys_init_setting';
    protected $primaryKey = 'OptionID';

    protected $fillable = [
        'OptionKey',
        'OptionName',
        'OptionValue',
        'DataType',
        'AllowChange',
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