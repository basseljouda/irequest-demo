<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStatusTrans extends Model
{
    protected $table = 'order_status_trans';
    
    public function order(){
        return $this->belongsTo(Orders::class,'order_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
