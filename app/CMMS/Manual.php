<?php

namespace App\CMMS;



class Manual extends CMMSModel
{
    
    protected $table = 'manuals';
    
    public function manuf() {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }
    
    public function files() {
        return $this->hasMany(ManualFile::class, 'manual_id');
    }

}
