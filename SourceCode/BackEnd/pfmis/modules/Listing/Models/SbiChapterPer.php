<?php
namespace Module\Listing\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SbiChapterPer extends Model{
    protected $table = 'sbi_chapter_per';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SbiChapterID',
        'SbiChapterName',
        'EmployeeID',
        'EmployeeName',
        'Access',
        'AccessField',
        'Edit',
        'EditField',
        'Delete',
        'Create',
    ];
    public $timestamps = false;

}

?>
