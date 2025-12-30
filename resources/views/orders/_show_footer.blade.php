<div class="r-panel-body p-3">
    <div class="row">
        @if ($order->status == 'pending')
        <div class="col-12">
        <textarea name="notes" class="form-control" id="notes" placeholder="@trans(Internal Notes)"></textarea>
        </div>
        @endif
        <div class="row col-12">
            <div class="col-3 text-left">
                <a href="#" class="dismiss-btn btn btn-default right-side-toggle w160">Dismiss</a>
            </div>
            <div class="col-9 text-right">
            @if ($order->status == 'pending' && $order->submited_at=='')
            @permission('add_serials')       
            <a href="#" class="status_change btn btn-info bg-{{$order->status}} w160">@trans(Submit) @trans(Assets)</a>
            @endpermission
            @elseif ($order->status == 'inroute' && $order->submited_at!='')
            @permission('confirm_delivered')
            <a href="#" class="status_change btn btn-info bg-{{$order->status}} w160">Confirm Delivery</a>
            @endpermission
            @elseif ($order->status == 'inconsignment')
            <a href="#" class="status_change btn btn-primary activate-order w160">Activate Order</a>
            @elseif ($order->status == 'delivered')
            @if ($order->consignment_order ==1)
            <a href="#" class="status_change btn btn-primary activate-order w160">Confirm Consignment</a>
            @else
            @permission('confirm_accepted')
            <a href="#" class="status_change btn btn-info bg-{{$order->status}} w160">Confirm Accepted</a>
            @endpermission
            @endif
            @elseif ($order->status == 'accepted')
            @permission('confirm_completed')
            <a href="#" class="status_change btn btn-info bg-{{$order->status}} w160">Complete Order</a>
            @endpermission
            @elseif ($order->status == 'completed')

            @permission('confirm_pickedup')
            @if ($order->pickup_requested > 1)
            <label class='text-danger mr-3' style="font-size: 120%;text-decoration: underline">Pickup has been requested for this order</label>
            @endif
            <a href="#" class="status_change btn btn-info pickup bg-{{$order->status}} w160">PickUp</a>

            @endpermission
            @permission('confirm_reassigned')

            <a href="#" class="status_change btn btn-info bg-reassigned reassign w160">Reassign</a>

            @endpermission
            @endif
            </div>
        </div>
    </div>
</div>
