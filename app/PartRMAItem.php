<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartRMAItem extends Model
{
    use SoftDeletes;

    protected $table = 'part_rma_items';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'part_rma_id',
        'part_id',
        'part_title',
        'part_oem',
        'part_price',
        'original_qty',
        'rma_qty',
        'price_condition',
        'product_condition',
        'return_reason',
        'return_type',
        'uploads',
        'comments',

    ];
    
    protected $casts = [
        'uploads' => 'array',
    ];

    public function rma()
    {
        return $this->belongsTo(PartRMA::class, 'part_rma_id');
    }

    public function part()
    {
        return $this->belongsTo(PartOrderDetail::class, 'part_id');
    }
    
    public function details()
    {
        return $this->hasMany(PartRmaItemsDetail::class, 'part_rma_items_id');
    }
}
