<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickupRequestDetails extends Model
{
   protected $table = 'pickup_request_details';
   
   protected $fillable = ['pickup_request_id','order_equipment_id','description'];


   public function equipment() {
        return $this->belongsTo(OrdersEquipments::class, 'order_equipment_id');
    }
    
    public function request() {
        return $this->belongsTo(PickupRequest::class, 'pickup_request_id');
    }
}
