<div class="modal-header">
    <h4 class="modal-title">Add New Equipment to Inventory: <strong class="text-warning">{{strtoupper($inventory->name)}}</strong></h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="addInventoryItem" class="ajax-form mb-3" method="{{isset($inventoryItem) ? 'PUT' : 'POST'}}" action="{{ isset($inventoryItem) ? route('admin.inventory.item-update',$inventoryItem->id) : route('admin.inventory.item-store') }}" autocomplete="off">
    @csrf
    <input type="hidden" name="inventory_id" value="{{$inventory->id}}"/>
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group" style="max-height: 350px">
                        <label>Equipment *</label>
                        <table id="eq_table" style="width: 99%">
                            <tr class="tr_clone">
                                <td style='width:45%'>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <select required="" class="select2 items m-b-10 form-control equipments"
                                                    data-placeholder="Select Equipment" name="equipments[]">
                                                <option value=""></option>
                                                @foreach($equipments as $equipment)
                                                <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>
                                <!--td>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="input-group" style="width:150px;margin-left:25%">
                                            <span class="input-group-prepend">
                                                <button type="button" class="btn btn-outline-secondary modal-btn-number" data-type="minus" data-field="item_balance[]">
                                                    <span class="fa fa-minus"></span>
                                                </button>
                                            </span>
                                            <input required type="text" name="item_balance[]" class="form-control modal-input-number" value="1" min="1" max="1000">
                                            <span class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary modal-btn-number" data-type="plus" data-field="item_balance[]">
                                                    <span class="fa fa-plus"></span>
                                                </button>
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td-->
                                <td>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control form-control-lg" id="dot_value" name="dot_value[]" placeholder="DOT #">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control form-control-lg" id="serial_value" name="serial_value[]" placeholder="Serial">
                                        </div>
                                    </div>
                                </td>
                                <td style='text-align: right' >
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <button class="btn btn-primary tr_clone_add" type="button"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
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
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script>
$('.items').select2();
$("#eq_table").on('click', '.tr_clone_add', function () {
    $('.items').select2("destroy");
    var $tr = $(this).closest('.tr_clone');
    var $clone = $tr.clone();
    $tr.after($clone);
    $('.items').select2();
    $clone.find("input.modal-input-number").val(1).change();
    $clone.find('.items').select2('val', '');
    $clone.find('.tr_clone_add').
            removeClass('tr_clone_add').
            removeClass('btn-primary').
            addClass('tr_clone_remove').
            addClass('btn-danger').
            html('<i class="fa fa-minus"></i>');
});
$("#eq_table").on('click', '.tr_clone_remove', function () {
    $(this).closest('.tr_clone').remove();
});

$('#addInventoryItem').submit(function (e) {
    e.preventDefault();
    
    const form = $(this);

    $.easyAjax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        container: '#addInventoryItem',
        data: $(this).serialize(),
        files: true,
        success: function (response) {
            if (response.status == 'success') {
                if ($('.dataTable').is(':visible')) {
                    table.draw();
                    $('#myTable').find('tr').css('opacity', '1.0');
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
$('.modal').on('click', 'button.modal-btn-number', function (e) {
    e.preventDefault();
    fieldName = $(this).attr('data-field');
    type = $(this).attr('data-type');
    var input = $(this).closest('tr').find("input.modal-input-number");
    var currentVal = parseInt(input.val());

    if (!isNaN(currentVal)) {
        if (type == 'minus') {

            if (currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            }
            if (parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }


        } else if (type == 'plus') {

            if (currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if (parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }


        }
    } else {
        input.val(0);
    }
});
$('.modal').on('focusin', '.modal-input-number', function (e) {
    $(this).data('oldValue', $(this).val());
});
$('.modal').on('change', '.modal-input-number', function (e) {
    e.preventDefault();
    minValue = parseInt($(this).attr('min'));
    maxValue = parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());

    name = $(this).attr('name');
    if (valueCurrent >= minValue) {
        $(".modal-btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if (valueCurrent <= maxValue) {
        $(".modal-btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }
});
$('.modal').on('keydown', '.modal-input-number', function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
            // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

</script>