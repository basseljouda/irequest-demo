<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupIssue extends Model
{
    use HasFactory;

    protected $table = 'pickup_issues';

    protected $fillable = [
        'order_id',
        'order_equipment_id',
        'user_id',
        'missing_details',
        'status',
        'resolved_at',
        'resolved_by',
        'resolved_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function orderEquipment()
    {
        return $this->belongsTo(OrdersEquipments::class, 'order_equipment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolvedByUser()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}