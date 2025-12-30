<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipments extends IDNModel {

    public function company() {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function modality() {
        return $this->belongsTo(Modality::class, 'modality_id');
    }

    public function sub_modality() {
        return $this->belongsTo(SubModality::class, 'sub_modality_id');
    }

    public function manufacturer() {
        return $this->belongsTo(EquipmentManufacturer::class, 'manufacturer_id');
    }

    public static function active() {
        return Equipments::where('active', 1);
    }

    public function testSheetTemplates() {
        return $this->belongsToMany(TestSheetTemplate::class, 'test_sheet_template_equipment', 'equipment_id', 'template_id');
    }

}
