<style>
    .hover-group {
        background-color: #fff; /* Initial background color */

    }

    .hover-group:hover {
        background-color: aliceblue; /* Hover color */
    }
</style>
<div class="modal-header">
    <h4 class="modal-title">@trans(Pickup requests for) #{{$order->id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="completeForm" class="ajax-form mb-3" action="{{ route('orders.send-request') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{ $order->id }}" />
    <div class="modal-body">

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="main-tab" data-toggle="tab" href="#main" role="tab" aria-controls="main" aria-selected="true">New Pickup Request</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="item-requests-tab" data-toggle="tab" href="#item-requests" role="tab" aria-controls="item-requests" aria-selected="false">Previous Requests</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cancelled-requests-tab" data-toggle="tab" href="#cancelled-requests" role="tab" aria-controls="item-requests" aria-selected="false">Cancelled Requests</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-2" id="myTabContent">
            <!-- Main Tab -->
            <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="main-tab">
                <div class="row font-12 col-12">
                    <h3 class="section-title-green">Select items to stop the billing and request a pickup</h3>

                    <table class="table order-table table-items modal-assets-table">
                        <thead></thead>
                        <tbody>
                            @if ($order->id <= $old_orders_id)
                            @foreach ($order->equipments as $item)
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" name="equipments_ids[]" value="{{ $item->id }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="items[]" id="itemCheckbox_{{ $item->id }}" value="{{ $item->id }}">
                                        <label class="form-check-label" for="itemCheckbox_{{ $item->id }}">
                                            <b> {{ $item->equipment->name }}</b>
                                            @if(\App\InventoryItems::where("id", $item->serial_no)->first()!==null)
                                            <label><u>{{ \App\InventoryItems::where("id", $item->serial_no)->first()->serial_value.' / '.
                                                \App\InventoryItems::where("id", $item->serial_no)->first()->dot_value}}</u></label>
                                            @endif
                                        </label>

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
                                        <input class="form-check-input" type="checkbox" name="items[]" id="itemCheckbox_{{ $item->id }}" value="{{ $item->id }}">
                                        <label class="form-check-label" for="itemCheckbox_{{ $item->id }}">
                                            <b> {{ $item->equipment->name }}</b>
                                        </label>
                                        <p>
                                        <ul>
                                            @if($item->assets != '')
                                            @foreach($order->viewAssets($item->id) as $asset)
                                            <li>{{ $asset->name }}</li>
                                            @endforeach
                                            @endif
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
                <div class="modal-footer">
                    <div class="col-6">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Request)</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="item-requests" role="tabpanel" aria-labelledby="item-requests-tab">
                <table class="table table-requests nowrap">
                    <tr class="bg-whitesmoke table-header-gray">
                        <th>#</th>
                        <th>Requested On</th>
                        <th>Requested By</th>
                        <th>Pickup Location</th>
                        <th>Contact</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>

                    @foreach ($order->pickupRequests as $pickup_request)

                    <tbody class="hover-group">
                        <tr>
                            <td>{{ $pickup_request->id }}</td>
                            <td>{{ dformat($pickup_request->created_at) }}</td>
                            <td>{{ $pickup_request->user->name }}</td>
                            <td>{{ $pickup_request->pickup_location }}</td>
                            <td>{{ $pickup_request->contact_phone }}</td>
                            <td>{{ $pickup_request->user->note }}</td>
                            <td>
                                @if ($pickup_request->request_version == 2)
                                @if ($pickup_request->requestFor->status == 'completed')
                                <a class="text-danger" onclick="cancelPickup({{ $pickup_request->id }})" href="javascript:;">@trans(Cancel Pickup Request)</a>
                                @else
                                <b class="text-success">Order is {{ucwords($pickup_request->requestFor->status)}}</b>
                                @endif
                                @endif
                            </td>
                        </tr>
                        <tr class="request-row-separator">
                            <td colspan="7" class="{{ $pickup_request->requestFor->status != 'completed' ? 'text-strikethrough' : '' }}">
                                @foreach ($pickup_request->items as $item)
                                {!! $item->description !!}<br/>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>      
            </div>

            <div class="tab-pane fade" id="cancelled-requests" role="tabpanel" aria-labelledby="cancelled-requests-tab">


                <table class="table table-requests nowrap">
                    <tr class="bg-whitesmoke table-header-gray">
                        <th>#</th>
                        <th>Cancelled On</th>
                        <th>Cancelled By</th>
                        <th>Staff Name</th>
                        <th>Signature</th>
                        <th>Notes</th>
                    </tr>

                    @foreach ($order->pickupRequests()->onlyTrashed()->orderby('deleted_at','desc')->get() as $pickup_request)

                    <tbody class="hover-group">
                        <tr>
                            <td>{{ $pickup_request->id }}</td>
                            <td>{{ dformat($pickup_request->deleted_at) }}</td>
                            <td>{{ $pickup_request->deleteBy->name ?? '' }}</td>
                            <td>{{ isset($pickup_request->deletedStaff) ? $pickup_request->deletedStaff->fullName() : '' }}</td>
                            <td><a href="{{route('orders.cancel_pickup_note',[$pickup_request->id])}}" target="_blank">Cancellation Form</a></td>
                            <td>{{ $pickup_request->delete_reason }}</td>
                        </tr>
                        <tr>
                            <td colspan="6" class="cancelled-request-separator">
                                @foreach ($pickup_request->items as $item)
                                {!! $item->description !!}<br/>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                    @endforeach
                </table>

            </div>
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
            $('.modal').modal('hide');
            document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('show');
            });
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.remove();
            });
            $("body").removeClass("control-sidebar-slide-open");
            window.refreshOrdersTable();
            }
            }
    });
    });

</script>