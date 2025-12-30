<div class="modal-header">
    <h4 class="modal-title">@trans(Pickup request for) #{{$order->id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="completeForm" class="ajax-form mb-3" action="{{ route('orders.send-request') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">


        <div class="row font-12 col-12">
            <h3 style="font-size: 15px;color:green ">Select Equipment to request a pickup</h3>
            <table class="table order-table" style="border-bottom: solid 1px #ddd;">
                <thead>
                </thead>
                <tbody>
                    @php $requests = \App\PickupRequest::where('order_id', $order->id)->get()@endphp
                    @if ($requests->count() > 0)
                    <table class="table table-sm" style="zoom: 80%">
                        <tr class="bg-whitesmoke" style="color: gray">
                                    <th>Requested On</th>
                                    <th>Requested By</th>
                                    <th>Pickup Location</th>
                                    <th>Contact</th>
                                    <th>Notes</th>
                                    <th></th>
                                </tr>
                    @foreach ($requests as $request)
                    @php $pickup = false @endphp
                    @if(\App\OrdersEquipments::where("id",$request->order_equipment_id)->wherenull('completed_date')->count()==0)
                    @php $pickup = true @endphp
                    @endif
                    <tr>
                        <td style="{{$pickup ? 'text-decoration: line-through' : ''}}" colspan="5"><b style="color:gray">{!!$request->description!!}</b></td>
                        <td>
                            @if (!$pickup)
                            <a class="text-danger" onclick="cancelPickup({{$request->id}})" href="javascript:;">@trans(Cancel Pickup Requests)</a>
                            @else
                            <b class="text-success">Item is Picked up</b>
                            @endif
                        </td>
                    </tr>
                    @if (!$pickup)
                    <tr>
                                    <td>{{$request->created_at}}</td>
                                    <td>{{$request->user->name}}</td>
                                    <td>{{$request->pickup_location}}</td>
                                    <td>{{$request->contact_phone}}</td>
                                    <td>{{$request->notes}}</td>
                                    <td></td>
                    </tr>
                    @endif
                    @endforeach
                    </table>
                    @endif
                    
                    @foreach ($order->equipments as $item)
                    @if($item->assets!= '' && $item->removed_assets!= '' && empty(array_diff($item->assets, $item->removed_assets))) @continue @endif
                    @php $eqDesc = '<span class="text-info">'.$item->equipment->name.'</span><br/>'.$item->asset_no.'  '.$item->serial_no@endphp 
                    <tr style="padding:10px">
                        <td style="padding:10px">
                            @php $request = \App\PickupRequest::where('order_equipment_id', $item->id)->first()@endphp
                            @if (!isset($request))
                            <div class="">
                                <input class="form-check-input" type="checkbox" name="equipment[]" id="eq{{$item->id.$loop->index}}" value="{{$item->id.'##'.$eqDesc}}">
                                <label class="form-check-label text-left" for="eq{{$item->id.$loop->index}}" style="">
                                    <b>{!!$eqDesc!!}</b>
                                </label>
                                <hr/>
                            </div>
                            @endif 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr/>
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