<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItems extends Model
{
    use HasFactory;
    
     protected $fillable = ['serial_value','dot_value'];
    
    public function equipment() {
        return $this->belongsTo(Equipments::class, 'equipment_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
