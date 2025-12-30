<style>
    .form-control:disabled, .form-control[readonly]{
        background-color: whitesmoke;
        font-weight: bold;
    }
    strong{
        font-size: 100%;
        font-weight: 700;
    }
    label{
        font-size: smaller;
        font-weight: normal !important;
    }
    #signature {
        width:-webkit-fill-available;
        box-shadow: 0 0 5px 1px #ddd inset;
        border:dashed 2px #53777A;
        border: dashed 1px #53777A;
        margin:0;
        text-align:center;

        transition:.2s;
        max-height: 130px;
    }
    canvas{
        max-height: 130px !important;
    }
</style>
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
            <table class="table order-table" style="border-bottom: solid 1px #ddd;">
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
            <div class="col-6">
                <div class="form-group">
                    <label class="add-modal"  onclick="showModal('{{ route('admin.hospital-staff.create') }}', '#staff-modal')">Staff member</span></label>
                    <select  class="select2 m-b-10 form-control " 
                             data-placeholder="Select Staff Member" name="staff" id="staff">
                        @foreach ($staff as $item)
                        <option @if ($item->id == $order->staff_id) selected @endif value="{{ $item->id }}">{{ ucwords($item->firstname .' '.$item->lastname) }}</option>   
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Acceptance) @trans(Notes)</label>
                    <textarea style="height: 34px;min-height: 34px;max-height: 110px" name="notes"  class="form-control" id="notes"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <span style="font-size:smaller;display: inline">Staff Signature:</span>
            </div>
            <div class="col-6 text-right">
                <a href="#" id="reset" style="font-size:smaller;display: inline" class="btn btn-danger btn-sm">Clear Signature</a>
            </div>
            <div id="signature" ></div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6 text-left">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>

        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-success"><i class="fa fa-check"></i> Accept</button>
        </div>
    </div>
    <textarea id="signature_capture" name="staff_signature" class="hide"></textarea>
</form>

<script>
    $('#staff').select2({
    tags: true
    });
    $('#acceptForm').submit(function (e) {
    e.preventDefault();
    const form = $(this);
    $.easyAjax({
    url: form.attr('action'),
            type: 'POST',
            container: '#acceptForm',
            data: $('#acceptForm').serialize(),
            file: true,
            success: function (response) {
            if (response.status == 'success') {
            $('.modal').modal('hide');
            $("body").removeClass("control-sidebar-slide-open");
            if (typeof table !== 'undefined') {
            table.draw();
            }
            }
            }
    });
    });</script>

<!--[if lt IE 9]>
<script type="text/javascript" src="libs/flashcanvas.js"></script>
<![endif]-->
<script src="{{ asset('assets/plugins/jSignature/jSignature.min.js?v=2')}}"></script>
<script>
    $(document).ready(function() {
    var $sigdiv = $("#signature").jSignature();
    $('#signature').bind('change', function(e) {
    var data = $('#signature').jSignature('getData');
    $("#signature_capture").val(data);
    });
    });
    $('#reset').click(function(e){
    $("#signature").jSignature("reset");
    $("#signature_capture").val('');
    e.preventDefault();
    });
</script>