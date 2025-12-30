<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketDetails extends Model
{
   protected $table = 'tickets_details';
   
   public function items() {
        return $this->belongsTo(TicketsItems::class, 'item_id');
    }
}
