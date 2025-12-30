<div class="modal-header">
    <h4 class="modal-title">Complete Order #{{$order->id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="completeForm" class="ajax-form mb-3" action="{{ route('orders.submitStatus') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <input type="hidden" name='complete' value="1"/>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label class="">Bill Start DateTime</label>
                    <input required="" type="text" class="form-control datepiktime" id="bill_start" value="{{dformat($order->bill_started,false)}}" name="bill_start" placeholder="Select date">
                </div>
            </div>  
            <div class="col-4">
                <div class="form-group">
                    <label>Delivery DateTime</label>
                    <input required="" type="text" class="form-control datepiktime" id="bill_close" value="{{dformat($order->delivered_at,false)}}" name="delivered_at" placeholder="Select date">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label class="text-danger text-bold">Bill End Date</label>
                    <input required="" type="text" class="form-control datepik" id="bill_completed" value="{{dformat('now',true)}}" name="bill_completed" placeholder="Select date">
                </div>
            </div>  
        </div>
        <div class="row font-12 col-12">
            <h3 class="section-title-green">Select Assets to Complete</h3>
            <table class="table order-table modal-assets-table">
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
                    @if ($item->completed_date != null) @continue; @endif
                <input type="hidden" name="equipments_ids[]" value="{{ $item->id }}">

                @if($item->assets != '')
                <tr>

                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="items[]" id="itemCheckbox_{{$item->id}}" value="{{$item->id}}">
                            <label class="form-check-label" for="itemCheckbox_{{$item->id}}">
                                <b>{{$item->equipment->name}}</b>
                            </label>
                        </div>

                    </td>
                    <td>
                        <ul>
                            @foreach($order->viewAssets($item->id) as $asset)
                            @if (is_array($item->removed_assets) && in_array($asset->id, $item->removed_assets)) @continue; @endif
                            <li>{{$asset->name}}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>@trans(Internal) @trans(Notes)</label>
                    <textarea name="notes" class="form-control" id="notes"></textarea>
                </div>
            </div>
        </div>
        @permission('confirm_pickedup')
        <!--hr/>
        <div class="row">
            <div class="col-6 offset-3">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="make_pickup" id="make_pickup">
                        <label class="form-check-label" for="make_pickup" style="margin-top: 10px">
                            Make order as <b class="text-pick">Picked Up</b><small>&nbsp;(Return to inventory)</small>
                        </label>                        
                    </div>
                </div>
            </div>
            <div class="">
                <div class="form-group">
                    <input required="" type="text" class="form-control datepik hide" id="pickup_date" value="{{dformat('now',true)}}" name="pickup_date" placeholder="Enter Bill End Date">
                </div>
                
            </div>
        </div-->
        @endpermission
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> Complete</button>
        </div>
    </div>
</form>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/orders/orders-modals.js?v='.$build_version) }}"></script>