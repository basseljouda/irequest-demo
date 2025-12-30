<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartRequest extends LinkedModel {

    use SoftDeletes;

    protected $table = 'part_requests';
    protected $fillable = [
        'user_id', 'status','staff_id' ,'hospital_id', 'contact_name', 'contact_phone', 'contact_email', 'date_needed', 'notes'
    ];
    protected $dates = ['date_needed', 'deleted_at','rfq_requested_on','rfq_replied_on','rfq_rejected_on'];

    public function details() {
        return $this->hasMany(PartRequestDetail::class, 'part_request_id');
    }
    
    public function rma() {
        return $this->hasMany(PartRMA::class, 'request_id')->latest('updated_at');
    }
    
    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function rfqRequestedBy() {
        return $this->belongsTo(User::class, 'rfq_requested_by');
    }
    
    public function rfqRepliedBy() {
        return $this->belongsTo(User::class, 'rfq_replied_by');
    }
    
    public function rfqRejectedBy() {
        return $this->belongsTo(User::class, 'rfq_rejected_by');
    }
    
    public function staff() {
        return $this->belongsTo(HospitalsStuff::class, 'staff_id');
    }
    
    public function orderRequest()
    {
        return $this->hasMany(OrderRequest::class, 'request_id')->orderby('updated_at','desc');
    }

}
