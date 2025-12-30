<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartRMA extends LinkedModel
{
    use SoftDeletes;

    protected $table = 'part_rma';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'request_id',
        'order_id',
        'hospital_id',
        'shipping_label',
        'status',
        'return_reason',
        'return_type',
        'contact_name',
        'contact_phone',
        'contact_email',
        'photos',
        'additional_comments',
        'user_id',
    ];

    // Specify attributes that should be cast to native types
    protected $casts = [
        'photos' => 'array',
    ];

    // Define the relationship with the PartRmaItem model
    public function items()
    {
        return $this->hasMany(PartRMAItem::class, 'part_rma_id');
    }
    
    public function itemsDetails()
    {
        return $this->hasMany(PartRmaItemsDetail::class, 'rma_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function request() {
        return $this->belongsTo(PartRequest::class, 'request_id');
    }
    
    public function order() {
        return $this->belongsTo(OrderRequest::class, 'order_id');
    }
}
