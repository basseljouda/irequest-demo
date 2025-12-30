<div class="modal-header">
    <h4 class="modal-title">Reassign Order #{{$order->order_id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="reassignForm" class="ajax-form mb-3" action="{{ route('orders.submitStatus') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <input type="hidden" name='reassign' value="1"/>
        <p><span class="small hide-in-create">Enter all required fields to reassign & create a new order</span></p>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Patient Name / ID<span class="required"> *</span></label>
                    <input required type="text" class="form-control" id="patient_name" name="patient_name" placeholder="Name or ID Number" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="text-danger">Bill Start Date *</label>
                    <input required="" type="text" class="form-control datepiktime" id="bill_start" value="{{dformat('now',false)}}" name="bill_start" placeholder="Select date">

                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Room<span class="required"> *</span></label>
                    <input type="text" required="" class="form-control" id="room" name="room" placeholder="Enter Room">
                </div>
            </div>
            @include('orders.components.staff-selector', ['staff' => $staff, 'order' => $order, 'required' => true])
        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label>Floor / Unit<span class="required"> *</span></label>
                    <input type="text" required="" class="form-control" id="unit_floor" name="unit_floor" placeholder="Enter floor / unit">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="add-modal1">Cost Center<span class="required"> *</span></label>
                    <select class="select2 m-b-10 form-control " required="" 
                            data-placeholder="Select cost center" name="cost_center" id="cost_center">
                        @foreach ($cost_centers as $item)
                        <option @if ($item->id == $order->cost_center_id) selected @endif value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" id="notes" placeholder="Enter your notes"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> Reassign</button>
        </div>
    </div>
</form>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/orders/orders-modals.js') }}"></script>