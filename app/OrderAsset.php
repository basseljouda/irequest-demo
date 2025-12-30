<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderAsset extends Model
{
    protected $table = 'order_assets';
    protected $fillable = ['asset_id','order_id','order_eq_id','title'];
}
