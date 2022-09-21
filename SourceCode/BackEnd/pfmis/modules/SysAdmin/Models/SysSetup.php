<?php
namespace Module\SysAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SysSetup extends Model{
    protected $table = 'sysglobaloption';
    protected $primaryKey = 'OptionID';

    protected $fillable = [
        'OptionName'
    ];

    public $timestamps = false;

    public static function getOption($optionKey){
        $content = Storage::disk('local')->get('config.json');
        $option = json_decode($content, true);
        return $option[$optionKey];
    }
}

?>