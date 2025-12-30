<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model {

    protected $fillable = ['name', 'code', 'favorite', 'hospital_id', 'unit_floor'];
 
    public function orders() {
        return $this->hasMany(Orders::class, 'cost_center_id');
    }
    
    public static function active() {
        return CostCenter::where('active',1);
    }

}
