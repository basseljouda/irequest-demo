<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartOrderDetail extends Model
{
    protected $table = 'part_order_details';

    protected $fillable = [
       'order_id', 'request_id', 'part_title', 'part_oem', 'price_type', 'qty','part_price','condition'
    ];

    public function order()
    {
        return $this->belongsTo(OrderRequest::class, 'order_id');
    }
    
    public function request()
    {
        return $this->belongsTo(PartRequest::class, 'request_id');
    }
}
