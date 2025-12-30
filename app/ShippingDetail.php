<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingDetail extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipping_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'parcel_length',
        'parcel_width',
        'parcel_height',
        'distance_unit',
        'parcel_weight',
        'mass_unit',
        'tracking_number',
        'carrier',
        'service_name',
        'service_object_id',
        'estimated_delivery_date',
        'status',
        'shipment_object_id',
        'rate_object_id',
        'cost_amount',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the order that owns the shipping detail.
     */
    public function order()
    {
        return $this->belongsTo(OrderRequest::class);
    }

    /**
     * Get the user associated with the shipping detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
