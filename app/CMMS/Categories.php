<?php

namespace App\CMMS;

class Categories extends CMMSModel
{
    protected $table = 'categories';
    
    public function catalog() {
        return $this->hasOne(Manual::class, 'category');
    }
    
    public function files() {
        return $this->hasMany(ManualFile::class, 'category');
    }
}
