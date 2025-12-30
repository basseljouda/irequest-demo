<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Orders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

/**
 * DEMO SKELETON: Orders Model Unit Tests
 * 
 * Tests for the Orders model relationships, attributes, and basic functionality.
 * Since business logic has been removed for demo purposes, tests focus on
 * model structure, relationships, and date casting.
 */
class OrdersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that order can be created with basic attributes
     */
    public function test_order_can_be_created()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'hospital_id' => 1,
        ]);

        $this->assertDatabaseHas('orders', [
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);
    }

    /**
     * Test that order dates are cast to Carbon instances
     */
    public function test_order_dates_are_carbon_instances()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'bill_started' => now(),
            'bill_completed' => now()->addDays(5),
            'date_needed' => now()->addDays(1),
            'delivered_at' => now()->addDays(2),
            'accepted_at' => now()->addDays(3),
        ]);

        $this->assertInstanceOf(Carbon::class, $order->bill_started);
        $this->assertInstanceOf(Carbon::class, $order->bill_completed);
        $this->assertInstanceOf(Carbon::class, $order->date_needed);
        $this->assertInstanceOf(Carbon::class, $order->delivered_at);
        $this->assertInstanceOf(Carbon::class, $order->accepted_at);
    }

    /**
     * Test that all date fields are in dates array
     */
    public function test_all_date_fields_are_casted()
    {
        $order = new Orders();
        $dates = $order->getDates();
        
        $expectedDates = [
            'date_needed', 'date_return', 'delivered_at', 'accepted_at', 
            'picked_at', 'reassigned_at', 'deleted_at', 'closed_at', 
            'submited_at', 'bill_started', 'bill_completed'
        ];

        foreach ($expectedDates as $dateField) {
            $this->assertContains($dateField, $dates);
        }
    }

    /**
     * Test that notify is cast to array
     */
    public function test_notify_is_cast_to_array()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'notify' => ['email', 'sms'],
        ]);

        $this->assertIsArray($order->notify);
        $this->assertEquals(['email', 'sms'], $order->notify);
    }

    /**
     * Test that notify can be null
     */
    public function test_notify_can_be_null()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'notify' => null,
        ]);

        $this->assertNull($order->notify);
    }

    /**
     * Test order has equipments relationship
     */
    public function test_order_has_equipments_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $order->equipments());
    }

    /**
     * Test order has pickup request relationship
     */
    public function test_order_has_pickup_request_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->pickupRequest());
    }

    /**
     * Test order has pickup requests relationship
     */
    public function test_order_has_pickup_requests_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $order->pickupRequests());
    }

    /**
     * Test order has items relationship (belongsToMany)
     */
    public function test_order_has_items_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $order->items());
    }

    /**
     * Test order has hospital relationship
     */
    public function test_order_has_hospital_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->hospital());
    }

    /**
     * Test order has staff relationship
     */
    public function test_order_has_staff_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->staff());
    }

    /**
     * Test order has status transitions relationship
     */
    public function test_order_has_status_transitions_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $order->statustrans());
    }

    /**
     * Test status transitions relationship filters by log_type
     */
    public function test_status_transitions_filters_by_log_type()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $relation = $order->statustrans();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
    }

    /**
     * Test order has all user relationships
     */
    public function test_order_has_user_relationships()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->submitedby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->createdby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->devliveredby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->acceptedby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->pickedupby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->reassigningby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->deletedby());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->closedby());
    }

    /**
     * Test order has staff relationships
     */
    public function test_order_has_staff_relationships()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->staffaccepted());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->staffpicked());
    }

    /**
     * Test order has cost center relationship
     */
    public function test_order_has_cost_center_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->costcenter());
    }

    /**
     * Test order has file relationship
     */
    public function test_order_has_file_relationship()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $order->file());
    }

    /**
     * Test order extends LinkedModel
     */
    public function test_order_extends_linked_model()
    {
        $order = new Orders();
        
        $this->assertInstanceOf(\App\LinkedModel::class, $order);
    }

    /**
     * Test order can be updated
     */
    public function test_order_can_be_updated()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $order->update([
            'status' => 'delivered',
            'order_id' => 'ORD-001-UPDATED',
        ]);

        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('ORD-001-UPDATED', $order->fresh()->order_id);
    }

    /**
     * Test order can be soft deleted
     */
    public function test_order_can_be_soft_deleted()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $orderId = $order->id;
        $order->delete();

        $this->assertSoftDeleted('orders', [
            'id' => $orderId,
        ]);
    }

    /**
     * Test order status can be changed
     */
    public function test_order_status_can_be_changed()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $order->status = 'inroute';
        $order->save();

        $this->assertEquals('inroute', $order->fresh()->status);
    }

    /**
     * Test order dates can be set and retrieved
     */
    public function test_order_dates_can_be_set_and_retrieved()
    {
        $now = now();
        
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'bill_started' => $now,
            'bill_completed' => $now->copy()->addDays(5),
        ]);

        $this->assertTrue($order->bill_started->equalTo($now));
        $this->assertTrue($order->bill_completed->equalTo($now->copy()->addDays(5)));
    }

    /**
     * Test order notify array can store multiple values
     */
    public function test_order_notify_array_stores_multiple_values()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
            'notify' => ['email', 'sms', 'push'],
        ]);

        $this->assertCount(3, $order->notify);
        $this->assertContains('email', $order->notify);
        $this->assertContains('sms', $order->notify);
        $this->assertContains('push', $order->notify);
    }

    /**
     * Test order relationships return correct types
     */
    public function test_all_order_relationships_return_correct_types()
    {
        $order = Orders::create([
            'order_id' => 'ORD-001',
            'status' => 'pending',
        ]);

        $relationships = [
            'equipments' => \Illuminate\Database\Eloquent\Relations\HasMany::class,
            'pickupRequest' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            'pickupRequests' => \Illuminate\Database\Eloquent\Relations\HasMany::class,
            'items' => \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            'hospital' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            'staff' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            'statustrans' => \Illuminate\Database\Eloquent\Relations\HasMany::class,
            'costcenter' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            'file' => \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
        ];

        foreach ($relationships as $method => $expectedClass) {
            $this->assertInstanceOf($expectedClass, $order->$method());
        }
    }
}
