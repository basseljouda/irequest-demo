<?php

namespace App\CMMS;

class Manufacturer extends CMMSModel
{
    
    protected $table = 'manufacturer';

    public function items() {
        return $this->hasMany(InventoryItems::class, 'manufacturer');
    }
}
