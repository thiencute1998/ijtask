<?php
namespace Module\Listing\Models;

use Illuminate\Database\Eloquent\Model;


class SponsorLink extends Model{

    protected $table = 'sponsor_link';
    protected $primaryKey = 'LineID';

    protected $fillable = [
        'SponsorID', 'LinkNo', 'LinkName', 'LinkTable'
    ];
    public $timestamps = false;


}

?>
