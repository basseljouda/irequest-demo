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
    <h4 class="modal-title">Cancel Pickup for order #{{$order->order_id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<form id="pickupForm" class="ajax-form mb-3" action="{{ route('orders.pickup-request-delete',$pr->id) }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <div class="row">
            <h5>Pickup Request Details:</h5>
            <table class="table table-requests nowrap table-nohover pickup-cancel-table">
                        <tr class="bg-whitesmoke table-header-gray">
                            <th>#</th>
                            <th>Requested On</th>
                            <th>Requested By</th>
                            <th>Pickup Location</th>
                            <th>Contact</th>
                            <th>Notes</th>
                        </tr>       
                            <tbody>
                                <tr>
                                    <td>{{ $order->pickupRequest->id }}</td>
                                    <td>{{ dformat($order->pickupRequest->created_at) }}</td>
                                    <td>{{ $order->pickupRequest->user->name }}</td>
                                    <td>{{ $order->pickupRequest->pickup_location }}</td>
                                    <td>{{ $order->pickupRequest->contact_phone }}</td>
                                    <td>{{ $order->pickupRequest->user->note }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="{{ $order->pickupRequest->requestFor->status != 'completed' ? 'text-decoration: line-through' : '' }}">
                                        @foreach ($order->pickupRequest->items as $item)
                                            {!! $item->description !!}<br/>
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                    </table>      
            </div>
        <hr/>
        <div class="row">
            @include('orders.components.staff-selector', ['staff' => $staff, 'order' => $order])
            <div class="col-6">
                <div class="form-group">
                    <label>@trans(Cancellation) @trans(Notes)*</label>
                    <textarea required="" name="notes" class="form-control notes-textarea" id="notes"></textarea>
                </div>
            </div>
        </div>

        @include('orders.components.signature-capture', ['name' => 'signature', 'resetButtonClass' => 'btn-default'])
    </div>
    <div class="modal-footer">
        <div class="col-6 text-left">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>

        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-danger w160"><i class="fa fa-remove"></i> Cancel Pickup Request</button>
        </div>
    </div>
</form>

<script>
    $('.datepik').bootstrapMaterialDatePicker({format: 'MM/DD/YYYY', weekStart: 0, time: false});
    $('#staff').select2({
    tags: true
    });
    $('#pickupForm').submit(function (e) {
    e.preventDefault();
    const form = $(this);
    $.easyAjax({
    url: form.attr('action'),
            type: 'POST',
            container: '#pickupForm',
            data: $('#pickupForm').serialize(),
            file: true,
            success: function (response) {
            if (response.status == 'success') {
            $('.modal').modal('hide');
            document.querySelectorAll('.modal').forEach(modal => {
                 modal.classList.remove('show');
            });
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });
            $("body").removeClass("control-sidebar-slide-open");
            // Refresh orders DataTable
            window.refreshOrdersTable();
            }
            }
    });
    });</script>

<!--[if lt IE 9]>
<script type="text/javascript" src="libs/flashcanvas.js"></script>
<![endif]-->
<script src="{{ asset('assets/plugins/jSignature/jSignature.min.js?v='.$build_version)}}"></script>
<script src="{{ asset('js/orders/orders-signature.js?v='.$build_version) }}"></script>