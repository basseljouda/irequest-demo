<?php

namespace App;

class Hospitals extends LinkedModel
{
    public function company(){
        return $this->belongsTo(Company::class,'company_id');
    }
    
    public function staff(){
        return $this->hasMany(HospitalsStuff::class, 'hospital_id');
    }
}
