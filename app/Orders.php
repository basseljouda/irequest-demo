<?php

namespace App;

/**
 * DEMO SKELETON: Orders Model
 * 
 * PURPOSE & WORKFLOW:
 * This model represents medical equipment rental orders in the system.
 * 
 * Original Functionality:
 * - Managed order relationships with equipments, hospitals, staff, users, cost centers
 * - Tracked order status transitions and history
 * - Calculated revenue data from orders and rental days using complex SQL queries
 * - Generated chart data for revenue by hospital and monthly revenue breakdowns
 * - Exported revenue data for AI training and analytics
 * - Retrieved asset information from CMMS inventory system
 * - Aggregated order status counts and cost center totals
 * 
 * Key Features (Original):
 * 1. Relationships: Equipment, hospital, staff, users, cost centers, attachments, status transitions
 * 2. Revenue Calculations: Complex SQL queries joining orders, equipments, and fulldays tables
 * 3. Chart Data Generation: Revenue by hospital and monthly revenue aggregations
 * 4. Asset Management: Integration with CMMS inventory system for asset tracking
 * 5. Status Tracking: Order status counts and transitions
 * 6. Data Export: Revenue data export for analytics and AI training
 * 
 * For demo purposes, all business logic, calculations, and database queries have been removed.
 * Only Eloquent relationships are kept to maintain the model structure for portfolio presentation.
 */
class Orders extends LinkedModel {

    protected $dates = [
        'date_needed', 'date_return', 'delivered_at', 'accepted_at', 'picked_at', 'reassigned_at', 'deleted_at', 'closed_at', 'submited_at', 'bill_started', 'bill_completed'
    ];

    protected $casts = [
        'notify' => 'array'
    ];

    public function equipments() {
        return $this->hasMany(OrdersEquipments::class, 'order_id');
    }

    public function pickupRequest() {
        return $this->belongsTo(PickupRequest::class, 'pickup_requested');
    }

    public function pickupRequests() {
        return $this->hasMany(PickupRequest::class, 'order_id');
    }

    public function items() {
        return $this->belongsToMany(Equipments::class, OrdersEquipments::class, 'order_id', 'equipment_id');
    }

    public function hospital() {
        return $this->belongsTo(Hospitals::class, 'hospital_id');
    }

    public function staff() {
        return $this->belongsTo(HospitalsStuff::class, 'staff_id');
    }

    public function statustrans() {
        return $this->hasMany(OrderStatusTrans::class, 'order_id')
                        ->where('log_type', 'orders')
                        ->orderBy('id', 'desc');
    }

    public function submitedby() {
        return $this->belongsTo(User::class, 'submited_by');
    }

    public function createdby() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function devliveredby() {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function acceptedby() {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function staffaccepted() {
        return $this->belongsTo(HospitalsStuff::class, 'staff_accepted');
    }

    public function staffpicked() {
        return $this->belongsTo(HospitalsStuff::class, 'pickup_staff');
    }

    public function pickedupby() {
        return $this->belongsTo(User::class, 'picked_by');
    }

    public function reassigningby() {
        return $this->belongsTo(User::class, 'reassigned_by');
    }

    public function deletedby() {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function closedby() {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function costcenter() {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
    
    public function file() {
        return $this->belongsTo(Attachment::class, 'attachment_id');
    }

    /**
     * Removed: viewAssets($id) - Retrieved asset information from CMMS inventory system with complex joins and formatting
     * Removed: assets($id) - Retrieved assets query builder for specific equipment
     * Removed: getChart($type, $params) - Generated chart data for revenue by hospital and monthly revenue with permission-based filtering
     * Removed: getStatuses() - Aggregated order status counts from database
     * Removed: ordersSql($restricted, $dateFilters) - Generated complex SQL query for revenue calculations joining orders, equipments, fulldays tables
     * Removed: costcenters() - Aggregated order totals by cost center
     * Removed: exportRevenueData() - Exported revenue data to JSON file for AI training with complex SQL aggregations
     */

}
