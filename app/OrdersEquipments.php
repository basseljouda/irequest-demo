<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdersEquipments extends Model {

    protected $table = 'orders_equipments';
    protected $fillable = [
        'created_at', 'equipment_id', 'price_day', 'serial_no', 'quantity','assets','inventory_id','asset_no'
    ];
    protected $casts = [
        'assets' => 'array',
        'removed_assets' => 'array'
    ];
    protected $dates = ['completed_date'];

    public function order() {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function equipment() {
        return $this->belongsTo(Equipments::class, 'equipment_id');
    }
    
    public function inventory() {
        return $this->belongsTo(CMMS\Locations::class, 'inventory_id');
    }
    
     public function parent() {
        return $this->belongsTo(OrdersEquipments::class, 'parent_id');
    }
    
    public function pickupRequest() {
        return $this->hasOne(PickupRequest::class, 'order_equipment_id');
    }
    
    public function cmmsItems()
    {
        return $this->belongsToMany(CMMS\InventoryItems::class, 'assets', 'id', 'id');
    }

}
