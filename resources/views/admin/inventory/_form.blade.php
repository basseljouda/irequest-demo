<form id="addInventory" class="ajax-form mb-3" method="{{isset($inventory) ? 'PUT' : 'POST'}}" action="{{ isset($inventory) ? route('admin.inventory.update',$inventory->id) : route('admin.inventory.store') }}" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Description *</label>
                        <input required="" value="{{isset($inventory) ? $inventory->name : ''}}" type="text" class="form-control form-control-lg" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Code</label>
                        <input type="text" value="{{isset($inventory) ? $inventory->code : ''}}" class="form-control form-control-lg" id="code" name="code">
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" value="{{isset($inventory->address) ? $inventory->address : ''}}" class="form-control form-control-lg" id="address" name="address">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" value="{{isset($inventory->city) ? $inventory->city : ''}}" class="form-control form-control-lg" id="city" name="city">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" value="{{isset($inventory) ? $inventory->state : ''}}" class="form-control form-control-lg" id="state" name="state">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" value="{{isset($inventory) ? $inventory->zip : ''}}" class="form-control form-control-lg" id="zip" name="zip">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" value="{{isset($inventory) ? $inventory->notes : ''}}" class="form-control form-control-lg" id="notes" name="notes">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="create-inventory" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Save)</button>
        </div>
    </div>
</form>
<script>
    $('#addInventory').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            container: '#addInventory',
            data: $(this).serialize(),
            files:true,
            success: function (response) {
                if (response.status == 'success') {
                    if ($('.dataTable').is(':visible')) {
                        table.draw();
                    }
                    if ($('#inventory').is(':visible')) {
                        var newOption = new Option(response.text, response.id, false, false);
                        $('#inventory').append(newOption).trigger('change');
                        $('#inventory').val(response.id);
                    }

                    $('.modal').modal('hide');
                }
            }
        });
    });
</script>