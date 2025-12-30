<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestSheetTemplate extends Model {

    protected $fillable = ['name', 'is_active'];

    public function tasks() {
        return $this->hasMany(TestSheetTask::class, 'template_id');
    }

    /* public function equipments() {
      return $this->belongsToMany(Equipments::class, 'test_sheet_template_equipment', 'template_id', 'equipment_id');
      } */

    public function equipmentNames() {
        return $this->hasMany(TestSheetTemplateEquipment::class, 'template_id');
    }

}
