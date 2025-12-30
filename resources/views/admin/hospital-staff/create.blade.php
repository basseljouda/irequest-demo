<style>
    .hide-in-create{
        display: none;
    }
    .show-in-create{
        display: block !important;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title">@trans(New) @trans(Site) @trans(Staff)</h4>
    <button type="button" class="close mclose"  aria-hidden="true">×</button>
</div>
<div class="modal-body">

    <form id="staffform" class="ajax-form mb-3" action="{{ route('admin.hospital-staff.store') }}" method="POST" autocomplete="off">
        @include('admin.hospital-staff._form')
        @permission('edit_hospital_staff')
        <div class="form-group row">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="createuser" id="createuser">
                <label class="form-check-label" for="createuser">
                    Click here to Create a user login and send account information to their email
                </label>
            </div>
        </div>
        @endpermission
        <div class="modal-footer">
            <div class="col-6">
                <button type="button" class="mclose btn btn-default"><i class="fa fa-times"></i> @trans(Cancel)</button>
            </div>
            <div class="col-6 text-right">
                <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Save)</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        $('#staff-modal').css('z-index', '999999');
    });

    $('.mclose').click(function (e) {
        $('#staff-modal').modal('hide');
    })

    $('#staffform').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.easyAjax({
            url: form.attr('action'),
            type: 'POST',
            container: '#staffform',
            file: true,
            success: function (response) {
                if (response.status == 'success') {
                    if ($('.dataTable').is(':visible') && !$('.orders-table').is(':visible')) {
                        table.draw();
                    }
                    let newOption = new Option(response.text, response.id, false, false);
                    if ($('#staff').is(':visible')) {
                        $('#staff').append(newOption).trigger('change');
                        $('#staff').val(response.id);
                    } else if ($('#alert-new-div > #staff').is(':visible')) {
                        $('#alert-new-div > #staff').append(newOption).trigger('change');
                        $('#alert-new-div > #staff').val(response.id);
                    }
                    $('#staff-modal').modal('hide');
                }
                
            }
        });
    });
</script>