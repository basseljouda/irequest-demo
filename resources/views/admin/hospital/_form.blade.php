<form id="addHospital" class="ajax-form mb-3" method="{{isset($hospital) ? 'PUT' : 'POST'}}" action="{{ isset($hospital) ? route('admin.hospital.update',$hospital->id) : route('admin.hospital.store') }}" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>@trans(Name)*</label>
                        <input required="" value="{{isset($hospital) ? $hospital->name : ''}}" type="text" class="form-control form-control-lg" id="name" name="name">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>@trans(IDN)*</label>
                        <select  name="company_id" class="select2 form-control" required="">
                            @foreach ($companies as $item)
                            <option {{selected(isset($hospital) ? $hospital->company_id : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Site Type)*</label>
                        <select class="select2 m-b-10 form-control" 
                                data-placeholder="" name="type" id="type" required="">
                            <option {{selected(isset($hospital) ? $hospital->type : '','main')}} value="main">@trans(Main)</option>   
                            <option {{selected(isset($hospital) ? $hospital->type : '','mob')}} value="mob">@trans(MOB)</option>   
                        </select>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Entity No)</label>
                        <input type="text" value="{{isset($hospital) ? $hospital->entity_no : ''}}" class="form-control form-control-lg" id="entity_no" name="entity_no">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Address)</label>
                        <input type="text" value="{{isset($hospital->address) ? $hospital->address : ''}}" class="form-control form-control-lg" id="address" name="address">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Market)</label>
                        <input type="text" value="{{isset($hospital->city) ? $hospital->city : ''}}" class="form-control form-control-lg" id="city" name="city">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(State)</label>
                        <input type="text" value="{{isset($hospital) ? $hospital->state : ''}}" class="form-control form-control-lg" id="state" name="state">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(ZIP Code)</label>
                        <input type="text" value="{{isset($hospital) ? $hospital->zip : ''}}" class="form-control form-control-lg" id="zip" name="zip">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Phone)</label>
                        <input type="text" value="{{isset($hospital->phone) ? $hospital->phone : ''}}" class="form-control form-control-lg" id="phone" name="phone">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Notes)</label>
                        <input type="text" value="{{isset($hospital) ? $hospital->notes : ''}}" class="form-control form-control-lg" id="notes" name="notes">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Website)</label>
                        <input type="text" value="{{isset($hospital) ? $hospital->website : ''}}" class="form-control form-control-lg" id="website" name="website">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@trans(Image)</label>
                        <input type="file" id="input-file-now" name="image" accept=".png,.jpg,.jpeg" class="dropify"
                               data-default-file="{{ $user->profile_image_url  }}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="create-hospital" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> @trans(Save)</button>
        </div>
    </div>
</form>
<script>
    $('#addHospital').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            container: '#addHospital',
            data: $(this).serialize(),
            files: true,
            success: function (response) {
                if (response.status == 'success') {
                    if ($('.dataTable').is(':visible')) {
                        table.draw();
                    }
                    if ($('#hospital').is(':visible')) {
                        var newOption = new Option(response.text, response.id, false, false);
                        $('#hospital').append(newOption).trigger('change');
                        $('#hospital').val(response.id);
                    }

                    $('.modal').modal('hide');
                }
            }
        });
    });
</script>