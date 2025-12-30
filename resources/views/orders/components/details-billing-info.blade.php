{{-- Details Billing Info Component
    Parameters:
    - $order: Order model
    - $total_days: Total days
    - $order_total: Order total amount
--}}
<div class="row order-section billing-info-section">
    <div class="col-12 row">
        
        <div class="col">
            <label class="app-color">Bill Start</label><br />
            {{dformat($order->bill_started)}}
        </div>
        <div class="col">
            <label class="app-color">Bill End</label><br />
            <label>{{dformat($order->bill_completed,true)}}</label>
        </div>
        <div class="col">
            <label class="app-color">Order Days</label><br />
            {{$total_days}}
        </div>
        @permission('view_amount')
        <div class="col">
            <label class="app-color">Total</label><br />
            <label class="text-danger">${{number_format($order_total,2)}}</label>
        </div>
        @endpermission
    </div>
</div>

