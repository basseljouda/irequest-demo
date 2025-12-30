<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRequest extends LinkedModel {

    use HasFactory,
        SoftDeletes;

    protected $fillable = [
        'request_id',
        'date_needed',
        'hospital_id',
        'po_number',
        'po_file',
        'packing_slip',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'shipment_type',
        'shipment_method',
        'tracking_no',
        'account_no',
        'status',
        'user_id',
        'notes'
    ];
    
    protected $dates = ['date_needed','processed_on', 'shipped_on', 'completed_on', 'delivered_on', 'fulfill_on'];

    public function rma() {
        return $this->hasMany(PartRMA::class, 'order_id');
    }

    public function partRequest() {
        return $this->belongsTo(PartRequest::class, 'request_id');
    }

    public function shipment() {
        return $this->hasOne(ShippingDetail::class, 'order_id');
    }

    public function details() {
        return $this->hasMany(PartOrderDetail::class, 'order_id');
    }

    // Define relationship with User
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff() {
        return $this->belongsTo(HospitalsStuff::class, 'staff_id');
    }

    public function site() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }

    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }

    public function statustrans() {
        return $this->hasMany(OrderStatusTrans::class, 'order_id')
                        ->where('log_type', 'part_requests')
                        ->orderBy('id', 'desc');
    }

    public function getCustomID() {
        if ($this->partial_shipping_id > 0) {
            return $this->request_id . '/' . $this->partial_shipping_id;
        } else {
            return $this->request_id;
        }
    }

}
