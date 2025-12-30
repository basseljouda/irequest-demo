<div class="r-panel-body p-3">
    <div class="row">
       
        <div class="col-12">
        <textarea name="notes" class="form-control" id="notes" placeholder="@trans(Internal Notes)"></textarea>
        </div>
       
        <div class="row col-12">
            <div class="col-3 text-left">
                <a href="#" class="dismiss-btn btn btn-default right-side-toggle w160">Dismiss</a>
            </div>
            <div class="col-9 text-right">
            @if ($order->status == 'pending' && $order->submited_at=='')
                  
            <a href="#" class="status_changex btn btn-info bg-{{$order->status}} w160">@trans(Send Quote)</a>
            
            @elseif ($order->status == 'inroute')
            @permission('confirm_delivered')
            <a href="#" class="status_changex btn btn-info bg-{{$order->status}} w160">Convert To Processing</a>
            @endpermission
            @elseif ($order->status == 'inconsignment')
            <a href="#" class="status_changex btn btn-primary activate-order w160">Activate Order</a>
            @elseif ($order->status == 'delivered')
            @if ($order->consignment_order ==1)
            <a href="#" class="status_changex btn btn-primary activate-order w160">Confirm Consignment</a>
            @else
            @permission('confirm_accepted')
            <a href="#" class="status_changex btn btn-info bg-{{$order->status}} w160">Convert To Shipped</a>
            @endpermission
            @endif
            @elseif ($order->status == 'accepted')
            @permission('confirm_completed')
            <a href="#" class="status_changex btn btn-info bg-{{$order->status}} w160">Mark as Delivered</a>
            @endpermission
            @elseif ($order->status == 'completed')

            @permission('confirm_pickedup')

            <a href="#" class="status_changex btn btn-info pickup bg-{{$order->status}} w160">Close Order</a>

            @endpermission
            
            @endif
            </div>
        </div>
    </div>
</div>
