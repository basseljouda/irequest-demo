<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class HospitalsStuff extends LinkedModel
{
    use Notifiable;
    
    protected $table = 'hospitals_stuff';
    
    protected $fillable = [
       'email','phone', 'firstname', 'lastname', 'user_id', 'hospital_id', 'title_id', 'supervisor_name', 'supervisor_email'
    ];
    
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    
    public function hospital(){
        return $this->belongsTo(Hospitals::class,'hospital_id');
    }
    
    public function title(){
        return $this->belongsTo(StaffTitle::class,'title_id');
    }
    
    public function fullname() {
        return $this->firstname.' '.$this->lastname;
    }
    
    public function routeNotificationForNexmo($notification)
    {
        return $this->phone;
    }
}
