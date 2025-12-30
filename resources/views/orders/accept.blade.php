<link rel="stylesheet" href="{{ asset('css/orders-design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/orders.css') }}">
<div class="modal-header">
    <h4 class="modal-title">Accept Assets Delivery #{{$order->id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="acceptForm" class="ajax-form mb-3" action="{{ route('orders.submitStatus') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <input type="hidden" name='accept' value="1"/>
        <div class="row">
            <div class="col-4">
                <div class="form-group">
                    <label>Site</label>
                    <input disabled type="text" class="form-control" value="{{$order->hospital->name}}" />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label>Patient</label>
                    <input disabled type="text" class="form-control" value="{{$order->patient_name}}" />
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Unit</label>
                    <input disabled  type="text"  class="form-control" value="{{$order->unit_floor}}">
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label>Room</label>
                    <input disabled type="text"  class="form-control" value="{{$order->room_no}}">
                </div>
            </div>
        </div>
        <div class="row font-12 col-12">
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
                    @if($item->assets != '')
                    <tr>
                        <td>
                            {{$item->equipment->name.'  '}}
                        </td>
                        <td>
                            @foreach($order->viewAssets($item->id) as $asset)
                            <b>{{$asset->name}}</b>
                            @endforeach
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Delivery Date & Time:</label>
                    <strong>{{$order->delivered_at}}</strong>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Current Date & Time:</label>
                    <strong>{{\Carbon\Carbon::now()}}</strong>
                </div>
            </div>
        </div>

        <div class="row">
            @include('orders.components.staff-selector', ['staff' => $staff, 'order' => $order])
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Acceptance) @trans(Notes)*</label>
                    <textarea required="" name="notes" class="form-control notes-textarea" id="notes"></textarea>
                </div>
            </div>
        </div>
        <div class="">
            <div class="col-md-12 offset-3">
            <div class="form-group">
    <label class="label form-label"><b>Inservice Status </b><span class="text-danger">*</span></label>
    <label><input type="radio" name="inservice_status" value="accepted" required> Accepted Inservice</label>
    <label><input type="radio" name="inservice_status" value="declined" required> Declined Inservice</label>
</div></div>
        </div>
        @include('orders.components.signature-capture', ['name' => 'staff_signature', 'label' => 'Staff Signature'])
    </div>
    <div class="modal-footer">
        <div class="col-6 text-left">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>

        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-success"><i class="fa fa-check"></i> Accept</button>
        </div>
    </div>
</form>

<script src="{{ asset('assets/plugins/jSignature/jSignature.min.js?v=2')}}"></script>
<script src="{{ asset('js/orders/orders-modals.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/orders/orders-signature.js?v='.$build_version) }}"></script>