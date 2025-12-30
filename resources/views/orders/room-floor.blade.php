<div class="modal-header">
    <h4 class="modal-title">Change patient details for #{{$order->order_id}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="roomForm" class="ajax-form mb-3" action="{{ route('orders.showchangeRoom') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="id" value="{{$order->id}}" />
    <div class="modal-body">
        <input type="hidden" name='reassign' value="1"/>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Patient Name<span class="required"> *</span></label>
                    <input type="text" required="" class="form-control" id="patient_name" name="patient_name" value="{{$order->patient_name}}" placeholder="Enter patient name">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Room<span class="required"> *</span></label>
                    <input type="text" required="" class="form-control" id="room_no" name="room_no" value="{{$order->room_no}}" placeholder="Enter Room">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label>Floor / Unit<span class="required"> *</span></label>
                    <input type="text" required="" class="form-control" id="unit_floor" value="{{$order->unit_floor}}" name="unit_floor" placeholder="Enter floor / unit">
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
            <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> Change</button>
        </div>
    </div>
</form>
<script>
    window.orderIdForRoomChange = '{{$order->id}}';
</script>
<script src="{{ asset('js/orders/orders-modals.js?v='.$build_version) }}"></script>
<script>
    // Room form submission
    if ($('#roomForm').length) {
        $('#roomForm').submit(function (e) {
            e.preventDefault();
            const form = $(this);
            $.easyAjax({
                url: form.attr('action'),
                type: 'POST',
                container: '#roomForm',
                data: $('#roomForm').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        if (typeof showOrder === 'function' && window.orderIdForRoomChange) {
                            showOrder(window.orderIdForRoomChange, true);
                        }
                        $('.modal').modal('hide');
                        return false;
                    }
                }
            });
        });
    }
</script>