<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/orders-design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="modal-header">
    <h4 class="modal-title">Pickup Assets #{{$order->order_id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="pickupForm" class="ajax-form mb-3" action="{{ route('orders.submitStatus') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <input type="hidden" name='pickup' value="1"/>
        
        <div class="row">
            @include('orders.components.staff-selector', ['staff' => $staff, 'order' => $order])
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Pickup) @trans(Notes)*</label>
                    <textarea required="" name="notes" class="form-control notes-textarea" id="notes"></textarea>
                </div>
            </div>
        </div>
        
        
        <div class="row font-12 col-12">
            <h5 class="text-primary">Pickup Assets</h5>
            <table class="table order-table modal-assets-table">
                <thead>
                </thead>
                <tbody>
                    @if ($order->id <= $old_orders_id)
                    @foreach ($order->equipments as $key => $item)
                    <tr class="item-row">
                        <td colspan="2">
                            <div class="d-flex justify-content-between align-items-start">
                                <strong>
                                    {{$item->equipment->name.'  '.$item->asset_no.'  '.$item->serial_no}}
                                </strong>
                                <div class="switchery-demo">
                                    <label style="margin-right: 5px; font-size: smaller;">Missed/Damaged</label>
                                    <input type="checkbox" class="js-switch damage-toggle" 
                                           id="damage_toggle_{{$key}}" 
                                           name="items[{{$item->id}}][is_damaged]" 
                                           value="1"
                                           data-color="#ff9800" 
                                           data-size="small">
                                </div>
                            </div>
                            <div class="damage-details" id="damage_details_{{$key}}">
                                <label for="damage_notes_{{$key}}">Damage/Missing Details:</label>
                                <textarea class="form-control" 
                                          id="damage_notes_{{$key}}" 
                                          name="items[{{$item->id}}][damage_details]" 
                                          rows="3" 
                                          placeholder="Please describe visible damage or missing accessories..."></textarea>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    @foreach ($order->equipments as $key => $item)
                    @if($item->assets != '')
                    <tr class="item-row">
                        <td>
                            {{$item->equipment->name.'  '}}
                        </td>
                        <td>
                            <div class="d-flex justify-content-between align-items-start flex-column">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div>
                                        @foreach($order->viewAssets($item->id) as $asset)
                                        <b>{{$asset->name}}</b>
                                        @endforeach
                                    </div>
                                    <div class="switchery-demo">
                                        <label class="damage-toggle-label">Missed/Damaged</label>
                                        <input type="checkbox" class="js-switch damage-toggle" 
                                               id="damage_toggle_{{$key}}" 
                                               name="items[{{$item->id}}][is_damaged]" 
                                               value="1"
                                               data-color="#ff9800" 
                                               data-size="small">
                                    </div>
                                </div>
                                <div class="damage-details w-100" id="damage_details_{{$key}}">
                                    <label for="damage_notes_{{$key}}">Damage/Missing Details:</label>
                                    <textarea class="form-control" 
                                              id="damage_notes_{{$key}}" 
                                              name="items[{{$item->id}}][damage_details]" 
                                              rows="3" 
                                              placeholder="Please describe visible damage or missing accessories..."></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        

        @include('orders.components.signature-capture', ['name' => 'pickup_signature', 'label' => 'Pickup Signature'])
    </div>
    <div class="modal-footer">
        <div class="col-6 text-left">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>

        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-info bg-completed w160"><i class="fa fa-check"></i> Pickup</button>
        </div>
    </div>
</form>
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/jSignature/jSignature.min.js?v=2')}}"></script>
<script src="{{ asset('js/orders/orders-modals.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/orders/orders-signature.js?v='.$build_version) }}"></script>