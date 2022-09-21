<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;

class ToolLink extends Model{
    protected $table = 'tool_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'ToolID',
        'LinkID',
        'LinkNo',
        'LinkName',
        'LinkTable'
    ];
    public $timestamps = false;

}

?>
