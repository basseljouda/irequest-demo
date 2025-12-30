<?php

namespace App\CMMS;

class WorkOrder extends CMMSModel {
    
    protected $table = 'workorder';
    
    protected $fillable = ['work_type', 'work_requested','requested_on','requester_phone'
        , 'user_id', 'customer_id', 'type', 'project_id','purchase_no', 'priority', 'due_date', 
        'frequency', 'procedure_id', 'start_date', 'est_hours', 'imedical_location', 'requested_by',
        'assign_to', 'asset_id', 'notes','risk','problem','resolution','work_performed','response_time',
        'down_time','tech_level','physical_condition','ground_resistance','leakage_current'];

    protected $dates = [
        'start_date', 'due_date','requested_on', 'complete_date', 'close_date', 'accept_date', 'delete_date'];
    
    protected $casts = [
        'notify' => 'array'
    ];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }
    
    public function folder(){
        return 'orders';
    }
    public function customer() {
        return $this->belongsTo(Customer::class,'customer_id');
    }
    
    public function asset() {
        return $this->belongsTo(InventoryItems::class,'asset_id');
    }
    
    public function assigned() {
        return $this->belongsTo(User::class, 'assign_to');
    }
    
    public function createdby() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function acceptedby() {
        return $this->belongsTo(User::class, 'accepted_by');
    }
    
    public function deletedby() {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function closedby() {
        return $this->belongsTo(User::class, 'closed_by');
    }
    
    public function completedby() {
        return $this->belongsTo(User::class, 'completed_by');
    }
    
    public function file() {
        return $this->belongsTo(Attachment::class, 'attachment_id')->orderby('id');
    }
    
    public function parts() {
        return $this->hasMany(OrderPart::class, 'wo_id')->orderby('id');
    }
    
    public function labors() {
        return $this->hasMany(OrderLabor::class, 'wo_id')->orderby('id');
    }
    
    public function work() {
        return $this->hasMany(WorkPerformed::class, 'wo_id')->orderby('id');
    }
    
    public function tasks() {
        return $this->hasMany(OrderTask::class, 'wo_id')->orderby('id');
    }
    
    public function procedure() {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }
    
    public function schedule() {
        return $this->belongsTo(ScheduleOrder::class, 'schedule_id');
    }
}
