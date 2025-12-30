<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartRequestDetail extends Model
{
    protected $table = 'part_request_details';

    protected $fillable = [
        'part_request_id', 'part_title', 'part_oem', 'price_type', 'qty'
    ];

    public function partRequest()
    {
        return $this->belongsTo(PartRequest::class, 'part_request_id');
    }
}
