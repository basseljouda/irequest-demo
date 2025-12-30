<form id="addEquipment" class="ajax-form mb-3" method="{{isset($equipment) ? 'PUT' : 'POST'}}" action="{{ isset($equipment) ? route('admin.equipment.update',$equipment->id) : route('admin.equipment.store') }}" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label>@trans(IDN)*</label>
                        <select  name="company_id" class="select2 form-control" required="">
                            @foreach ($companies as $item)
                            <option {{selected(isset($equipment) ? $equipment->company_id : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Combo Description *</label>
                        <input required maxlength="100" value="{{isset($equipment) ? $equipment->name : ''}}" type="text" class="form-control form-control-lg" id="name" name="name">
                    </div>
                </div>
               
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Model *</label>
                        <input required value="{{isset($equipment) ? $equipment->model : ''}}" type="text" class="form-control form-control-lg" id="model" name="model">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>OEM No</label>
                        <input value="{{isset($equipment) ? $equipment->oem_no : ''}}" type="text" class="form-control form-control-lg" id="oem_no" name="oem_no">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>Price *</label>
                        <input required value="{{isset($equipment) ? $equipment->price_day : ''}}" type="text" class="form-control form-control-lg" id="price_day" name="price_day">
                    </div>
                </div>
            
            
                <div class="col-6">
                    <div class="form-group">
                        <label>Refurbished Price</label>
                        <input  value="{{isset($equipment) ? $equipment->refurbished_price : ''}}" type="text" class="form-control form-control-lg" id="refurbished_price" name="refurbished_price">
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
    $('#addEquipment').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            container: '#addEquipment',
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