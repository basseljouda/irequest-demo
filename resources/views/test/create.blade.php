<div class="modal-header">
    <h4 class="modal-title">Adding New Site</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <form id="addHospital" class="ajax-form mb-3" action="{{ route('hospital.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="form-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Name *</label>
                        <input required="" type="text" class="form-control form-control-lg" id="name" name="name">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control form-control-lg" id="phone" name="phone">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control form-control-lg" id="address" name="address">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" class="form-control form-control-lg" id="city" name="city">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" class="form-control form-control-lg" id="state" name="state">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" class="form-control form-control-lg" id="zip" name="zip">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Entity No</label>
                        <input type="text" class="form-control form-control-lg" id="entity_no" name="entity_no">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Notes</label>
                        <input type="text" class="form-control form-control-lg" id="notes" name="notes">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Website</label>
                        <input type="text" class="form-control form-control-lg" id="website" name="website">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify"
                               data-default-file="{{ $user->profile_image_url  }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions pull-right">
            <button type="submit" id="create-hospital" class="btn btn-success">@lang('app.add')</button>
        </div>
    </form>
</div>
<script>
    $('#addHospital').submit(function (e) {
        e.preventDefault();

        const form = $(this);

        $.easyAjax({
            url: form.attr('action'),
            type: 'POST',
            container: '#addHospital',
            file: true,
            success: function (response) {
                if (response.status == 'success') {
                    var newOption = new Option(response.text, response.id, false, false);
                    $('#hospital').append(newOption).trigger('change');
                    $('#hospital').val(response.id);
                    $('#OrderModal').modal('hide');
                }
            }
        });
    });
</script>