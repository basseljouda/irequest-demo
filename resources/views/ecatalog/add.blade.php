<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css') }}">
<form id="addpartssource" class="ajax-form mb-3" method="PUT" action="{{ route('ps.update',$partssource->myid)  }}" autocomplete="off">
    @csrf
    <div class="modal-body">
            <div class="form-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Qty *</label>
                            
                            <input value="{{isset($partssource) ? $partssource->qty : ''}}" maxlength="5" required type="number" class="form-control form-control-lg" id="qty" name="qty">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Location *</label>
                            <input required value="{{isset($partssource) ? $partssource->location : ''}}" type="text" class="form-control form-control-lg" id="location" name="location">
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
<script src="{{ asset('assets/plugins/jquery-bar-rating-master/dist/jquery.barrating.min.js') }}"></script>
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