<?php

namespace App\CMMS;


class ManualFile extends CMMSModel
{
    
    protected $table = 'manual_files';
    
    public function manual() {
        return $this->belongsTo(Manual::class, 'manual_id');
    }

}
