<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket extends Model
{
    protected $table = 'tickets';
    
    protected $dates = ['due_date','deleted_at','signed_at','inprogress_at','assigned_at','approved_at','picked_at'];
    
    public function createdby() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function deletedby() {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    
    public function assignedby() {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    public function assigned() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function inprogressby() {
        return $this->belongsTo(User::class, 'inprogress_by');
    }
    
    public function requested_staff() {
        return $this->belongsTo(HospitalsStuff::class, 'requested_by');
    }
    
    public function recipient_staff() {
        return $this->belongsTo(HospitalsStuff::class, 'recipient');
    }
    
    public function pickedupStaff() {
        return $this->belongsTo(HospitalsStuff::class, 'picked_signed_by');
    }
    
    public function signedstaff() {
        return $this->belongsTo(HospitalsStuff::class, 'signed_by');
    }
    
    
    public function pickedBy() {
        return $this->belongsTo(User::class, 'picked_by');
    }
    
    public function completedBy() {
        return $this->belongsTo(User::class, 'completed_by');
    }
    
    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function details() {
        return $this->hasMany(TicketDetails::class, 'ticket_id');
    }
    
    public function fromhospital() {
        return $this->belongsTo(Hospitals::class, 'from_hospital');
    }
    
     public function tohospital() {
        return $this->belongsTo(Hospitals::class, 'to_hospital');
    }
    public function file() {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }
   
}
