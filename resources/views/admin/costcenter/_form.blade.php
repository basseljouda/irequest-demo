<link rel="stylesheet" href="{{ asset('assets/plugins/jquery-bar-rating-master/dist/themes/fontawesome-stars.css') }}">
<form id="addCostcenter" class="ajax-form mb-3" method="{{isset($costcenter) ? 'PUT' : 'POST'}}" action="{{ isset($costcenter) ? route('admin.costcenter.update',$costcenter->id) : route('admin.costcenter.store') }}" autocomplete="off">
    @csrf
    <div class="modal-body">
            <div class="form-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Name *</label>
                            <input value="{{isset($costcenter) ? $costcenter->name : ''}}" maxlength="190" required type="text" class="form-control form-control-lg" id="name" name="name">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Code Number *</label>
                            <input required value="{{isset($costcenter) ? $costcenter->code : ''}}" type="text" class="form-control form-control-lg" id="code" name="code">
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-6">
                        <div class="form-group">
                            <label>Floor/Unit</label>
                            <input value="{{isset($costcenter) ? $costcenter->unit_fllor : ''}}" maxlength="20" type="text" class="form-control form-control-lg" id="unit_floor" name="unit_floor">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                             <label>Favorite</label>
                            <div class="stars">
                                <select class="favorite" id="favorite" name="favorite">
                                    <option value=""></option>
                                    @for($i=1;$i<6;$i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
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
$('#favorite').barrating({
    theme: 'fontawesome-stars',
    showSelectedRating: false,
});
@isset($costcenter)
$('#favorite').barrating('set', '{{$costcenter->favorite}}');
@endisset
$('#addCostcenter').submit(function (e) {
    e.preventDefault();
    const form = $(this);

    $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            container: '#addCostcenter',
            data: $(this).serialize(),
            files:true,
            success: function (response) {
                if (response.status == 'success') {
                    if ($('.dataTable').is(':visible')) {
                        table.draw();
                    }
                    $('.modal').modal('hide');
                }
            }
        });
});
</script>