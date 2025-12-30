<div class="modal-header">
    <h4 class="modal-title">New Inventory</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>

<form id="addpartssource" class="ajax-form mb-3" method="POST" action="{{ route('ps.post') }}" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>OEM P/N *</label>
                        <input  required type="text" class="form-control form-control-lg" id="partnumber" name="partnumber">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Title *</label>
                        <input required type="text" class="form-control form-control-lg" id="title" name="title">
                    </div>
                </div>
            </div>
           <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Description *</label>
                        <input required type="text" class="form-control form-control-lg" id="description" name="description">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Brand *</label>
                        <input required type="text" class="form-control form-control-lg" id="brand" name="brand">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Model *</label>
                        <input required type="text" class="form-control form-control-lg" id="models" name="models">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Price *</label>
                        <input required type="text" class="form-control form-control-lg" id="price" name="price">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Qty *</label>
                        <input required type="text" class="form-control form-control-lg" id="qty" name="qty">
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
            <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Save)</button>
        </div>
    </div>
</form>

<script>
        $('#addpartssource').submit(function (e) {
e.preventDefault();
const form = $(this);
$.easyAjax({
url: $(this).attr('action'),
        type: $(this).attr('method'),
        container: '#addpartssource',
        data: $(this).serialize(),
        files:true,
        success: function (response) {
        if (response.status == 'success') {
        
        $('.modal').modal('hide');
        }
        }
});
});
</script>
