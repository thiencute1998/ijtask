<?php
namespace Module\SysAdmin\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SysGroupMember extends Model{
    protected $table = 'sys_group_member';
    protected $primaryKey = 'MemberID';

    public static function search(Request $request){
        $data = null;
        $query = SysUser::query();
        return $query->paginate(SysSetup::getOption('NumberRowOnPage'));
    }

    public $timestamps = false;


}

?>