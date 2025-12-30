<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSheetTemplateEquipment extends Model {
    
    protected $fillable = ['template_id', 'equipment_name'];

    public function tasks()
    {
        return $this->hasMany(TestSheetTask::class, 'template_id');
    }

    /*public function equipments() {
        return $this->belongsToMany(Equipments::class, 'test_sheet_template_equipment', 'template_id', 'equipment_id');
    }*/

}
