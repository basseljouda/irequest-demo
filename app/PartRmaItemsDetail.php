<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartRmaItemsDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'part_rma_items_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rma_id',
        'part_rma_items_id',
        'serial_no',
        'user_resolution',
        'resolution_status',
        'customer_resolution',
        'user_notes',
        'customer_notes',
        'user_resolution_at',
        'customer_resolution_at',
        'user_id',
        'customer_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_resolution_at' => 'datetime',
        'customer_resolution_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    // Example: Defining a relationship to `PartRmaItem` if needed
    public function partRmaItem()
    {
        return $this->belongsTo(PartRMAItem::class, 'part_rma_items_id');
    }

    // Example: User who resolved the RMA
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Example: Customer associated with the RMA detail
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id'); // Assuming customers are stored in the `users` table
    }
}
