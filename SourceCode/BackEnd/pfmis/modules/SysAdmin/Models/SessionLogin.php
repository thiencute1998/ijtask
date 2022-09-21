<?php
namespace Module\SysAdmin\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SessionLogin extends Model{
    protected $table = 'session';

    protected $fillable = [
        'user_id', 'session_id', 'ip'
    ];
    public $timestamps = false;


}

?>