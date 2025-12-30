{{-- Form Basic Info Component
    Parameters:
    - $order: Order model (optional)
--}}
<div class="order-form-section">
    <div class="order-form-section-header">
        <h5 class="order-form-section-title">
            <i class="fa fa-info-circle"></i>
            Basic Information
        </h5>
    </div>
    
    @isset($order)
    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
        <div>
            <small class="text-muted">@trans(Last update): {{dformat($order->updated_at).' By: '.$order->createdby->name}}</small>
        </div>
        <div>
            @include('orders.components.status-badge', ['status' => $order->status, 'class' => 'cursor-pointer', 'text' => ucwords($order->status), 'onclick' => "showOrder('{$order->id}', true)"])
        </div>
    </div>
    @endisset
    
    <div class="row">
        @if (!auth()->user()->is_staff)
        <div class="col-md-3">
            <div class="form-group">
                <label>@trans(Sales Order)#</label>
                <input type="text" maxlength="10" value="{{isset($order->order_no) ? $order->order_no : ''}}" class="form-control" id="order_no" name="order_no" placeholder="Sales Order No" />
            </div>
        </div>
        @endif
        <div class="col-md-4">
            <div class="form-group">
                <label>@trans(Order Date) <span class="required"> *</span></label>
                <input type="text" required="" value="{{isset($order->created_at) ? dformat($order->created_at) : dformat('now')}}" class="form-control datepiktime" id="created_at" value="" name="created_at" placeholder="Select date">
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                @role('superadmin')
                @if (!isset($order))
                <div class="form-check" style="margin-top: 32px;">
                    <input class="form-check-input" type="checkbox" name="consignment_order" id="consignment_order">
                    <label class="form-check-label label-brown" for="consignment_order">
                        Order is in Consignment
                    </label>
                </div>
                @endif
                @endrole
            </div>
        </div>
    </div>
</div>

