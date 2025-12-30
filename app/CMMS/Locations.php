<?php

namespace App\CMMS;

use Illuminate\Database\Eloquent\Model;

class Locations extends CMMSModel
{
    protected $table = 'locations';
    
    public static function assets(){
        return Manufacturer::where('type','asset');
    }
}
