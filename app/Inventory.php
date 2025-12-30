<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    
    public function items() {
        return $this->hasMany(InventoryItems::class, 'inventory_id')
                ->whereHas('equipment', function($query) {
                    $query->where('active', 1);
                });
    }
    
    public function equipment() {
        return $this->belongsTo(Equipments::class, 'equipment_id');
    }

}
