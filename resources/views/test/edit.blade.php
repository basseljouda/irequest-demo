<style>
      .hover-group {
    background-color: #fff; /* Initial background color */
  }

  .hover-group:hover {
    background-color: aliceblue; /* Hover color */
  }
</style>
<div class="modal-header">
    <h4 class="modal-title">@trans(Pickup request for) #{{$order->id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="completeForm" class="ajax-form mb-3" action="{{ route('orders.send-request') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">


        <div class="row font-12 col-12">
            @if ($order->pickupRequests->count() > 0)
            @php
    $groupedRequests = $order->pickupRequests->groupBy('newOrder.id');
@endphp

<table class="table table-sm table-requests">
    <tr class="bg-whitesmoke" style="color: gray">
        <th>Order No</th>
        <th>Requested On</th>
        <th>Requested By</th>
        <th>Pickup Location</th>
        <th>Contact</th>
        <th>Notes</th>
        <th></th>
    </tr>
    
    @foreach ($groupedRequests as $orderId => $requests)
        @php 
            $pickup = $requests->first()->newOrder->status == 'pickedup';
            $descriptions = $requests->pluck('description')->unique(); // Collect unique descriptions
        @endphp
        <tbody  class="hover-group">
        <tr>
            <td>{{ $orderId }}</td>
            <td>{{ dformat($requests->first()->created_at) }}</td>
            <td>{{ $requests->first()->user->name }}</td>
            <td>{{ $requests->first()->pickup_location }}</td>
            <td>{{ $requests->first()->contact_phone }}</td>
            <td>{{ $requests->first()->user->note }}</td>
            <td>
                @if ($requests->first()->request_version == 2)
                    @if (!$pickup)
                        <a class="text-danger" onclick="cancelPickup({{ $requests->first()->id }})" href="javascript:;">@trans(Cancel Pickup Requests)</a>
                    @else
                        <b class="text-success">Order is Picked up</b>
                    @endif
                @endif
            </td>
        </tr>

        <!-- Display all descriptions in a single row -->
        <tr>
            <td colspan="7" style="{{ $pickup ? 'text-decoration: line-through' : '' }}">
                @foreach ($descriptions as $description)
                    {!! $description !!}<br/>
                @endforeach
            </td>
        </tr>
        </tbody>
    @endforeach
</table>

            @endif        
            <h3 style="font-size: 15px;color:green ">Select items to stop the billing and request a pickup</h3>
           
            <table class="table order-table table-items" style="border-bottom: solid 1px #ddd">
                <thead>
                </thead>
                <tbody>
                    @if ($order->id <= $old_orders_id)
                    @foreach ($order->equipments as $item)
                    <tr>
                        <td colspan="2">
                            <strong>
                                {{$item->equipment->name.'  '.$item->asset_no.'  '.$item->serial_no}}
                            </strong>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    @foreach ($order->equipments as $item)
                    
                

                @if ($item->completed_date == null) 
                <tr>

                    <td>
                        <input type="hidden" name="equipments_ids[]" value="{{ $item->id }}">
                        <div class="form-check">
                            
                              <input class="form-check-input" type="checkbox" name="items[]" id="itemCheckbox_{{$item->id}}" value="{{$item->id}}">
                            <label class="form-check-label" for="itemCheckbox_{{$item->id}}">
                                <b> {{$item->equipment->name}}</b>
                            </label>
                              <p>
                                  <ul>
                            @foreach($order->viewAssets($item->id) as $asset)
                           
                            <li>{{$asset->name}}</li>
                            @endforeach
                        </ul>
                              </p>
                                
                                
                        </div>

                    </td>
                    
                </tr>
                @endif
                @endforeach
                @endif
                </tbody>
            </table>

        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Pickup Location)*</label>
                    <input type="text" maxlength="300" required="" id="pickup_location" name="pickup_location" class="form-control"/>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Contact Phone)*</label>
                    <input type="text" maxlength="20" id="contact_phone" name="contact_phone" class="form-control" required=""/>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>@trans(Notes)</label>
                    <textarea name="notes" class="form-control" id="notes"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Request)</button>
        </div>
    </div>
</form>
<script>
    $('.datepiktime').bootstrapMaterialDatePicker({format: 'MM/DD/YYYY HH:mm', weekStart: 0, time: true});
    $('.datepik').bootstrapMaterialDatePicker({format: 'MM/DD/YYYY', weekStart: 0, time: false});
    $('#make_pickup').change(function (e) {
        if ($(this).prop('checked')) {
            $('#pickup_date').removeClass("hide");
        } else
            $('#pickup_date').addClass("hide");
    })
    $('#completeForm').submit(function (e) {
        e.preventDefault();
        const form = $(this);
        $.easyAjax({
            url: form.attr('action'),
            type: 'POST',
            container: '#completeForm',
            data: $('#completeForm').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    if (response.data) {
                        showOrder(response.data, true);
                        $('.modal').modal('hide');
                        return false;
                    }

                    $('.modal').modal('hide');
                    $("body").removeClass("control-sidebar-slide-open");
                    if (typeof table !== 'undefined') {
                        table.draw();
                    }


                }
            }
        });
    });
    function cancelPickup(id){
   
    swal({
        title: "@trans(Are you sure)",
        text: "Are you sure want to delete Pickup request?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "@trans(Yes)",
        cancelButtonText: "@trans(No)",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
             var url = "{{ route('orders.pickup-request-delete',':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'POST'},
                success: function (response) {
                    
                    if (response.status == "success") {
                        $('.modal').modal('hide');
                        if (typeof table !== 'undefined') {
                        table.draw();
                    }
                        $.unblockUI();
                    }
                }
            });
        }
    });
}
</script>