<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickupRequest extends Model
{
    use SoftDeletes;
    
   protected $table = 'pickup_request';
   protected $fillable = ['status','request_version' ,'order_id','user_id','pickup_location','contact_phone','notes'];

   static public $NEW_REQUEST = 'new';
   
    public function order() {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    
    public function requestFor() {
        return $this->hasOne(Orders::class, 'pickup_requested');
    }
    
     public function items() {
        return $this->hasMany(PickupRequestDetails::class, 'pickup_request_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function deletedBy() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    
    public function deletedStaff() {
        return $this->belongsTo(HospitalsStuff::class, 'delete_staff');
    }
}
